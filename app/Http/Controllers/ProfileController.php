<?php
namespace App\Http\Controllers;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\OrderItem;
use App\Models\Contact;



class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.info');
    }
    // Hiển thị trang chỉnh sửa thông tin tài khoản
    public function edit()
    {
        return view('profile.edit');
    }

    // Cập nhật thông tin tài khoản
    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để chỉnh sửa thông tin!');
        }

        $data = $request->validate([
            'name'          => ['required','string','max:255'],
            'phone'         => ['nullable','string','max:20'],
            'email'         => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'address'       => ['nullable','string','max:255'],
            'date_of_birth' => ['nullable','date'],
            'gender'        => ['nullable','in:Nam,Nữ,Khác'],
            'avatar'        => ['nullable','image','mimes:jpeg,png,jpg,gif','max:2048'],
        ]);

        $user->name = $data['name'];
        $user->phone = $data['phone'] ?? null;
        $user->email = $data['email'] ?? null;              // ✅ lưu email
        $user->address = $data['address'] ?? null;
        $user->dob = $data['date_of_birth'] ?? null;        // ✅ map đúng
        $user->gender = $data['gender'] ?? null;

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $avatarPath;
        }

        $user->save();

        return redirect()->route('profile.info')->with('success', 'Cập nhật thông tin thành công!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:6', 'confirmed'],
        ]);

        $user = Auth::user();

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('password_error', 'Mật khẩu hiện tại không đúng.');
        }

        // Không cho trùng mật khẩu cũ
        if (Hash::check($request->new_password, $user->password)) {
            return back()->with('password_error', 'Mật khẩu mới không được trùng mật khẩu cũ.');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('profile.info')->with('success', 'Đổi mật khẩu thành công ✅');
    }
    
    public function orders(Request $request)
    {
        $status = $request->get('status'); // lọc trạng thái (tuỳ chọn)
        $q      = trim($request->get('q', '')); // tìm theo mã đơn

        $orders = Order::with(['orderItems.product'])
            ->where('user_id', Auth::id())
            ->when($status, fn($qr) => $qr->where('status', $status))
            ->when($q !== '', fn($qr) => $qr->where('id', (int)$q))
            ->latest('id')
            ->paginate(10)
            ->appends(['status' => $status, 'q' => $q]);

        // map màu trạng thái cho badge
        $statusMap = [
            'pending'          => ['Chờ xác nhận', 'warning'],
            'awaiting_payment' => ['Chờ thanh toán', 'secondary'],
            'new'              => ['Mới', 'primary'],
            'processing'       => ['Đang xử lý', 'info'],
            'shipped'          => ['Đã gửi hàng', 'primary'],
            'completed'        => ['Hoàn tất', 'success'],
            'cancelled'        => ['Đã hủy', 'danger'],
        ];

        return view('profile.orders', compact('orders', 'statusMap', 'status', 'q'));
    }

        public function rebuy()
    {
        $user = Auth::user();

        $productIds = OrderItem::whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->whereNotNull('product_id')
            ->latest('id')
            ->pluck('product_id')
            ->unique()
            ->values();

        $perPage = 9;
        $currentPage = request()->get('page', 1);

        $pagedProductIds = $productIds->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $rebuyItems = OrderItem::with('product')
            ->whereIn('product_id', $pagedProductIds)
            ->whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest('id')
            ->get()
            ->unique('product_id')
            ->filter(fn ($item) => $item->product)
            ->sortBy(fn ($item) => array_search($item->product_id, $pagedProductIds->all()))
            ->values();

        $rebuyItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $rebuyItems,
            $productIds->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('profile.rebuy', compact('user', 'rebuyItems'));
    }

    public function faq()
    {
        $user = Auth::user();

        $faqs = [
            [
                'question' => 'Làm sao để đặt hàng?',
                'answer' => 'Bạn chọn sản phẩm, thêm vào giỏ hàng, sau đó vào trang thanh toán và điền thông tin nhận hàng.'
            ],
            [
                'question' => 'Tôi có thể hủy đơn hàng không?',
                'answer' => 'Bạn có thể hủy đơn khi đơn vẫn đang ở trạng thái chờ xác nhận hoặc chưa được xử lý.'
            ],
            [
                'question' => 'Shop có hỗ trợ đổi trả không?',
                'answer' => 'Shop hỗ trợ đổi trả theo chính sách đổi trả hiện hành, với sản phẩm còn nguyên vẹn và đúng điều kiện áp dụng.'
            ],
            [
                'question' => 'Bao lâu tôi nhận được hàng?',
                'answer' => 'Thời gian giao hàng thường từ 2 - 5 ngày tùy khu vực và đơn vị vận chuyển.'
            ],
        ];

        $myQuestions = Contact::where('email', $user->email)
            ->latest()
            ->get();

        return view('profile.faq', compact('user', 'faqs', 'myQuestions'));
    }

    public function sendQuestion(Request $request)
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ], [
            'subject.required' => 'Vui lòng nhập tiêu đề câu hỏi.',
            'message.required' => 'Vui lòng nhập nội dung câu hỏi.',
        ]);

        Contact::create([
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        return back();
    }
}
