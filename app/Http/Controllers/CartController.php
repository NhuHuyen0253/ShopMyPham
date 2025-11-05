<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;

class CartController extends Controller
{
    // POST /cart/add
    public function addToCart(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thêm vào giỏ hàng.',
            ], 401);
        }

        $validated = $request->validate([
            'product_id' => 'required|integer',
            'quantity'   => 'nullable|integer|min:1',
        ]);

        $productId = (int) $validated['product_id'];
        $qty       = (int) ($validated['quantity'] ?? 1);

        // kiểm tra sản phẩm tồn tại
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Sản phẩm không tồn tại.',
            ], 404);
        }

        // LẤY URL ẢNH (hỗ trợ thư mục con và tên thư mục đặc biệt)
        // ngay sau khi tìm thấy $product
$imageUrl = asset('images/product/'); // chỉnh đúng 1 đường dẫn thật


        // Ghi giỏ hàng vào session
        $cart = session()->get('cart', []);

        if (!isset($cart[$productId])) {
            $cart[$productId] = [
                'product_id' => $productId,
                'name'       => $product->name,
                'price'      => $product->price,
                'quantity'   => 0,
                'image_url'  => $imageUrl, // có thể null
            ];
        } else {
            // Bổ sung image_url cho item cũ nếu trước đây chưa có
            if (!isset($cart[$productId]['image_url'])) {
                $cart[$productId]['image_url'] = $imageUrl;
            }
        }

        $cart[$productId]['quantity'] += $qty;
        session()->put('cart', $cart);

        $count = array_sum(array_column($cart, 'quantity'));

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng.',
            'count'   => $count,
            'item'    => $cart[$productId],
        ]);
    }

    // GET /cart/count — trả về tổng số lượng item
    public function count(Request $request)
    {
        $cart  = session()->get('cart', []);
        $count = array_sum(array_column($cart, 'quantity'));
        return response()->json(['count' => $count]);
    }


    public function updateQty(Request $req)
{
    $pid = $req->input('product_id');  // Sửa lại cho đúng phương thức lấy dữ liệu
    $qty = max(1, (int) $req->input('quantity'));  // Cũng cần chắc chắn rằng số lượng là số nguyên dương

    // Kiểm tra giỏ hàng trong session
    $cart = session()->get('cart', []);
    if (!isset($cart[$pid])) {
        return response()->json(['ok' => false, 'message' => 'Không tìm thấy sản phẩm'], 404);
    }

    // Cập nhật số lượng
    $cart[$pid]['quantity'] = $qty;
    session()->put('cart', $cart);

    // Tính lại tổng tiền của sản phẩm và giỏ hàng
    $itemTotal = $cart[$pid]['price'] * $qty;  // Tính tiền cho sản phẩm hiện tại
    $subtotal  = array_sum(array_map(fn($p) => $p['price'] * $p['quantity'], $cart));  // Tính tổng tiền của giỏ hàng

    return response()->json([
        'ok'         => true,
        'item_total' => number_format($itemTotal, 0, ',', '.') . ' ₫',  // Trả về tổng tiền của sản phẩm hiện tại
        'subtotal'   => number_format($subtotal, 0, ',', '.') . ' ₫',    // Trả về tổng tiền của giỏ hàng
        'grand'      => number_format($subtotal, 0, ',', '.') . ' ₫',    // Tổng cộng
    ]);
}


    // POST /cart/remove
    public function remove(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'remove_all' => 'nullable|boolean', // true: xoá hẳn; false: giảm 1
        ]);

        $productId = (int) $validated['product_id'];
        $removeAll = (bool) ($validated['remove_all'] ?? true);

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            if ($removeAll || ($cart[$productId]['quantity'] ?? 0) <= 1) {
                unset($cart[$productId]);
            } else {
                $cart[$productId]['quantity'] -= 1;
            }
            session()->put('cart', $cart);
        }

        $count = array_sum(array_column($cart, 'quantity'));

        return response()->json([
            'success' => true,
            'message' => $removeAll ? 'Đã xóa khỏi giỏ.' : 'Đã giảm số lượng.',
            'count'   => $count,
        ]);
    }

    public function viewCart()
    {
        $cart = session()->get('cart', []);
        // Đảm bảo key image_url tồn tại để Blade xử lý hiển thị có điều kiện
        foreach ($cart as &$item) {
            if (!array_key_exists('image_url', $item)) {
                $item['image_url'] = null;
            }
        }
        unset($item);

        session()->put('cart', $cart);
        return view('Cart', ['cart' => $cart]);
    }

    /**
     * Suy ra URL ảnh sản phẩm theo các nguồn:
     * - image_rel_path (ví dụ "Banila-Co/sp1.jpg")
     * - brand_slug + image_name, hoặc brand + image_name
     * - quét thư mục public/images/product/** để tìm image_name (tối đa 3 cấp)
     */
    private function resolveProductImageUrl($product): ?string
    {
        // 1) image_rel_path (nếu có)
        $rel = $product->image_rel_path ?? null;

        // 2) Tự build từ brand_slug + image_name
        if (!$rel && !empty($product->brand_slug) && !empty($product->image_name)) {
            $rel = $product->brand_slug . '/' . $product->image_name;
        }

        // 3) Tự build từ brand (tên thương hiệu) + image_name
        if (!$rel && !empty($product->brand) && !empty($product->image_name)) {
            // Chuẩn hoá brand thành tên thư mục: thay khoảng trắng = '-', bỏ ký tự lạ (giữ chữ/số/_/-/')
            $brandFolder = preg_replace('~[^\w\-\' ]+~u', '', $product->brand);
            $brandFolder = trim($brandFolder);
            $brandFolder = preg_replace('~\s+~', '-', $brandFolder);
            $rel = $brandFolder . '/' . $product->image_name;
        }

        // Thử với $rel (nếu đã có)
        if (!empty($rel)) {
            $rel = trim(str_replace('\\', '/', $rel), '/'); // "\" -> "/", bỏ "/" thừa
            $abs = public_path('images/product/' . $rel);
            if (is_file($abs)) {
                $segments = array_map('rawurlencode', explode('/', $rel)); // encode từng segment
                return asset('images/product/' . implode('/', $segments));
            }
        }

        // 4) Quét thư mục theo image_name (phao cứu sinh nếu DB không lưu path)
        $imageName = $product->image_name ?? null;
        if ($imageName) {
            $imageName = basename($imageName);
            $base     = public_path('images/product');

            // Duyệt thư mục tối đa 3 cấp: product/, product/*, product/*/*
            $rii = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($base, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            $maxDepth = 3;
            foreach ($rii as $file) {
                if ($rii->getDepth() > $maxDepth) continue;
                if ($file->isFile() && strcasecmp($file->getFilename(), $imageName) === 0) {
                    $absFound = $file->getPathname();
                    $relFound = trim(str_replace('\\', '/', substr($absFound, strlen($base) + 1)), '/');
                    $segments = array_map('rawurlencode', explode('/', $relFound));
                    return asset('images/product/' . implode('/', $segments));
                }
            }
        }

        return null; // không tìm thấy
    }
}
