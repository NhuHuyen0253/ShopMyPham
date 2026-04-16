<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VnPayController extends Controller
{
    /**
     * Build hash data + query string theo chuẩn VNPay 2.1.0:
     * - sort key ASC
     * - bỏ param null / ''
     * - BOTH hash & query đều dùng urlencode(key)=urlencode(value)
     */
    private function buildVnpData(array $inputData): array
    {
        // bỏ null / '' (KHÔNG bỏ '0')
        $inputData = array_filter($inputData, fn($v) => !($v === null || $v === ''));

        ksort($inputData);

        $hashParts  = [];
        $queryParts = [];

        foreach ($inputData as $key => $value) {
            $k = urlencode((string) $key);
            $v = urlencode((string) $value);

            $hashParts[]  = $k . '=' . $v;
            $queryParts[] = $k . '=' . $v;
        }

        return [
            'sorted'       => $inputData,
            'hash_string'  => implode('&', $hashParts),
            'query_string' => implode('&', $queryParts),
        ];
    }

    /**
     * Verify chữ ký VNPay
     */
    private function verifySignature(array $inputData, string $hashSecret): bool
    {
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        $built = $this->buildVnpData($inputData);
        $calcHash = hash_hmac('sha512', $built['hash_string'], $hashSecret);

        return hash_equals($calcHash, (string) $vnp_SecureHash);
    }

    /**
     * Trả về base local để redirect người dùng về shop.local sau khi từ VNPay quay lại
     */
    private function localBaseUrl(): string
    {
        return rtrim((string) env('LOCAL_RETURN_URL', 'http://shop.local'), '/');
    }

    /**
     * Tạo link notice local
     */
    private function localNoticeUrl($orderId, array $query = []): string
    {
        $path = route('order.notice', $orderId, false); // lấy path, không lấy domain
        $url = $this->localBaseUrl() . $path;

        if (!empty($query)) {
            $url .= '?' . http_build_query($query);
        }

        return $url;
    }

    /**
     * Tạo thanh toán VNPay
     */
    public function create(Request $request, Order $order)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        // chặn thanh toán lại nếu đã paid
        if ((int) $order->is_paid === 1 || in_array($order->status, ['processing', 'completed'], true)) {
            return redirect()->route('order.notice', $order->id)
                ->with('error', 'Đơn hàng đã thanh toán.');
        }

        $vnp_TmnCode    = trim((string) config('vnpay.tmn_code'));
        $vnp_HashSecret = trim((string) config('vnpay.hash_secret'));
        $vnp_Url        = trim((string) config('vnpay.url'));
        $vnp_Returnurl  = trim((string) config('vnpay.return_url')); // lấy từ env/config, KHÔNG dùng route()

        if ($vnp_TmnCode === '' || $vnp_HashSecret === '' || $vnp_Url === '' || $vnp_Returnurl === '') {
            return redirect()->route('order.notice', $order->id)
                ->with('error', 'Thiếu cấu hình VNPay.');
        }

        // IP: không dùng localhost
        $ip = $request->ip();
        if (in_array($ip, ['127.0.0.1', '::1', '', null], true)) {
            $ip = '8.8.8.8';
        }

        $vnp_TxnRef    = $order->id . '_' . time();
        $vnp_OrderInfo = 'Thanh toan don hang ' . $order->id;

        // đánh dấu đang chờ thanh toán
        $order->update([
            'payment_method' => 'vnpay',
            'status'         => 'awaiting_payment',
            'is_paid'        => 0,
        ]);

        $inputData = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $vnp_TmnCode,
            'vnp_Amount'     => (int) $order->total * 100,
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => $ip,
            'vnp_Locale'     => 'vn',
            'vnp_OrderInfo'  => $vnp_OrderInfo,
            'vnp_OrderType'  => 'billpayment',
            'vnp_ReturnUrl'  => $vnp_Returnurl,
            'vnp_TxnRef'     => $vnp_TxnRef,
            'vnp_ExpireDate' => date('YmdHis', strtotime('+30 minutes')),
        ];

        if ($request->filled('bank_code')) {
            $inputData['vnp_BankCode'] = $request->input('bank_code');
        }

        $built = $this->buildVnpData($inputData);
        $vnpSecureHash = hash_hmac('sha512', $built['hash_string'], $vnp_HashSecret);

        $paymentUrl = $vnp_Url . '?' . $built['query_string'] . '&vnp_SecureHash=' . $vnpSecureHash;

        Log::info('VNPAY CREATE', [
            'order_id'       => $order->id,
            'txn_ref'        => $vnp_TxnRef,
            'return_url'     => $vnp_Returnurl,
            'payment_url'    => $paymentUrl,
            'hash_string'    => $built['hash_string'],
            'query_string'   => $built['query_string'],
            'client_ip'      => $ip,
        ]);

        return redirect()->away($paymentUrl);
    }

    /**
     * Khách thanh toán xong VNPay redirect về public return URL
     * Sau đó hệ thống sẽ redirect tiếp về shop.local
     */
    public function return(Request $request)
    {
        $vnp_HashSecret = trim((string) config('vnpay.hash_secret'));
        $inputData = $request->all();

        Log::info('VNPAY RETURN', [
            'ip'  => $request->ip(),
            'all' => $inputData,
        ]);

        if (!$this->verifySignature($inputData, $vnp_HashSecret)) {
            return redirect()->away($this->localBaseUrl() . '/')
                ->with('error', 'Sai chữ ký VNPay');
        }

        $txnRef = (string) $request->input('vnp_TxnRef');
        $orderId = explode('_', $txnRef)[0] ?? null;

        $order = Order::find($orderId);
        if (!$order) {
            return redirect()->away($this->localBaseUrl() . '/')
                ->with('error', 'Không tìm thấy đơn hàng.');
        }

        $responseCode = (string) $request->input('vnp_ResponseCode');
        $transactionNo = (string) $request->input('vnp_TransactionNo');
        $bankCode = (string) $request->input('vnp_BankCode');
        $payDate = (string) $request->input('vnp_PayDate');

        if ($responseCode === '00') {
            if ((int) $order->is_paid !== 1) {
                $order->update([
                    'status'         => 'processing',
                    'is_paid'        => 1,
                    'payment_method' => 'vnpay',
                ]);
            }

            Log::info('VNPAY RETURN SUCCESS', [
                'order_id'        => $order->id,
                'txn_ref'         => $txnRef,
                'transaction_no'  => $transactionNo,
                'bank_code'       => $bankCode,
                'pay_date'        => $payDate,
            ]);

            return redirect()->away($this->localNoticeUrl($order->id, [
                'vnpay' => 'success',
                'code'  => $responseCode,
            ]));
        }

        // thất bại / hủy
        $order->update([
            'status'         => 'cancelled',
            'is_paid'        => 0,
            'payment_method' => 'vnpay',
        ]);

        Log::warning('VNPAY RETURN FAILED', [
            'order_id'       => $order->id,
            'txn_ref'        => $txnRef,
            'response_code'  => $responseCode,
            'transaction_no' => $transactionNo,
        ]);

        return redirect()->away($this->localNoticeUrl($order->id, [
            'vnpay' => 'failed',
            'code'  => $responseCode,
        ]));
    }

    /**
     * IPN: VNPay server gọi tới URL public
     * KHÔNG redirect về shop.local ở đây
     */
    public function ipn(Request $request)
    {
        Log::info('VNPAY IPN ARRIVED', [
            'method' => $request->method(),
            'ip'     => $request->ip(),
            'all'    => $request->all(),
        ]);

        $vnp_HashSecret = trim((string) config('vnpay.hash_secret'));
        $inputData = $request->all();

        if (!$this->verifySignature($inputData, $vnp_HashSecret)) {
            return response()->json([
                'RspCode' => '97',
                'Message' => 'Invalid signature',
            ]);
        }

        $txnRef = (string) $request->input('vnp_TxnRef');
        $orderId = explode('_', $txnRef)[0] ?? null;

        $order = Order::find($orderId);
        if (!$order) {
            return response()->json([
                'RspCode' => '01',
                'Message' => 'Order not found',
            ]);
        }

        $responseCode = (string) $request->input('vnp_ResponseCode');
        $paidAmount = ((int) $request->input('vnp_Amount')) / 100;

        // check amount
        if ((int) $order->total !== (int) $paidAmount) {
            return response()->json([
                'RspCode' => '04',
                'Message' => 'Invalid amount',
            ]);
        }

        // nếu đã paid rồi thì trả OK luôn
        if ((int) $order->is_paid === 1) {
            return response()->json([
                'RspCode' => '02',
                'Message' => 'Order already confirmed',
            ]);
        }

        if ($responseCode === '00') {
            $order->update([
                'status'         => 'completed',
                'is_paid'        => 1,
                'payment_method' => 'vnpay',
            ]);

            Log::info('VNPAY IPN SUCCESS', [
                'order_id' => $order->id,
                'txn_ref'  => $txnRef,
            ]);
        } else {
            $order->update([
                'status'         => 'cancelled',
                'is_paid'        => 0,
                'payment_method' => 'vnpay',
            ]);

            Log::warning('VNPAY IPN FAILED', [
                'order_id'      => $order->id,
                'txn_ref'       => $txnRef,
                'response_code' => $responseCode,
            ]);
        }

        return response()->json([
            'RspCode' => '00',
            'Message' => 'Confirm Success',
        ]);
    }
}