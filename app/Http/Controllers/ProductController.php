<?php

namespace App\Http\Controllers;

use App\Models\Brands;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        $brands = Brands::all();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'price'               => 'required|numeric|min:0',
            'quantity'            => 'required|integer|min:0',
            'category_id'         => 'required|exists:categories,id',
            'brand_id'            => 'required|exists:brands,id',
            'description'         => 'nullable|string',
            'usage_instructions'  => 'nullable|string',
            'is_hotdeal'          => 'nullable|boolean',             // <--
            'image'               => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'images.*'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        // Map tường minh
        $product = new Product();
        $product->name               = $validated['name'];
        $product->price              = $validated['price'];
        $product->quantity           = $validated['quantity'];
        $product->category_id        = $validated['category_id'];
        $product->brand_id           = $validated['brand_id'];
        $product->description        = $request->input('description');
        $product->usage_instructions = $request->input('usage_instructions');
        $product->is_hotdeal         = $request->boolean('is_hotdeal'); // <--

        // Ảnh chính
        if ($request->hasFile('image')) {
            $file  = $request->file('image');
            if ($file->isValid()) { // <--
                $fname = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
                $dest  = public_path('images/product');
                if (!is_dir($dest)) { @mkdir($dest, 0775, true); }
                $file->move($dest, $fname);
                if (\Schema::hasColumn('products', 'image')) {
                    $product->image = $fname;
                }
            }
        }

        $product->save();

        // Gallery
        $galleryPath = 'images/product/gallery';
        if (!is_dir(public_path($galleryPath))) {
            @mkdir(public_path($galleryPath), 0775, true);
        }

        $alts  = (array) $request->input('alt_new', []);
        $sorts = (array) $request->input('sort_new', []);
        $i = 0;

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if (!$file->isValid()) continue;
                $fname = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
                $file->move(public_path($galleryPath), $fname);

                ProductImage::create([
                    'product_id' => $product->id,
                    'file_name'  => $fname,
                    'path'       => $galleryPath,
                    'alt'        => $alts[$i] ?? null,
                    'sort_order' => is_numeric($sorts[$i] ?? null) ? (int) $sorts[$i] : 0,
                ]);
                $i++;
            }
        }

        return redirect()->route('admin.product.index')->with('success', 'Tạo sản phẩm thành công');
    }

    public function show($id)
    {
        $product = Product::with('images')->findOrFail($id);
        return view('show', compact('product'));
    }

    public function edit($id)
    {
        $product    = Product::with('images')->findOrFail($id);
        $categories = Category::all();
        $brands     = Brands::all();
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::with('images')->findOrFail($id);

        $validated = $request->validate([
            'name'                => 'required|string|max:255',
            'price'               => 'required|numeric|min:0',
            'quantity'            => 'required|integer|min:0',
            'category_id'         => 'required|exists:categories,id',
            'brand_id'            => 'required|exists:brands,id',
            'description'         => 'nullable|string',
            'usage_instructions'  => 'nullable|string',
            'is_hotdeal'          => 'nullable|boolean',             // <--
            // Ảnh chính: form dùng "image"; hỗ trợ thêm "main_image" nếu có
            'image'               => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            'main_image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            // Ảnh minh hoạ mới
            'images.*'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
            // Xoá ảnh: hỗ trợ cả hai tên mảng
            'remove_image_ids.*'  => 'nullable|integer',
            'delete_images.*'     => 'nullable',
        ]);

        // Map tường minh
        $product->name               = $validated['name'];
        $product->price              = $validated['price'];
        $product->quantity           = $validated['quantity'];
        $product->category_id        = $validated['category_id'];
        $product->brand_id           = $validated['brand_id'];
        $product->description        = $request->input('description');
        $product->usage_instructions = $request->input('usage_instructions');
        $product->is_hotdeal         = $request->boolean('is_hotdeal'); // <--

        // Ảnh chính: nhận 'image' (ưu tiên) hoặc 'main_image'
        $mainFile = $request->file('image') ?: $request->file('main_image');
        if ($mainFile) {
            $fname = Str::uuid()->toString().'.'.$mainFile->getClientOriginalExtension();
            $dest  = public_path('images/product');
            if (!is_dir($dest)) { @mkdir($dest, 0775, true); }
            $mainFile->move($dest, $fname);

            // Xoá ảnh cũ nếu tồn tại
            if (!empty($product->image)) {
                $old = public_path('images/product/'.$product->image);
                if (is_file($old)) @unlink($old);
            }
            if (\Schema::hasColumn('products', 'image')) {
                $product->image = $fname;
            }
        }

        $product->save();

        /* ====================== XÓA ẢNH GALLERY ====================== */
        $removeIds = (array) $request->input('remove_image_ids', []);
        $deleteAny = (array) $request->input('delete_images', []);

        // Xoá theo ID (từ bảng product_images)
        if (!empty($removeIds)) {
            $imgs = ProductImage::whereIn('id', $removeIds)
                ->where('product_id', $product->id)
                ->get();

            foreach ($imgs as $img) {
                $full = public_path(trim($img->path, '/').'/'.$img->file_name);
                if (is_file($full)) @unlink($full);
                $img->delete();
            }
        }

        // Xoá theo chuỗi đường dẫn (khi form gửi URL/filename, không có id)
        foreach ($deleteAny as $v) {
            if (ctype_digit((string) $v)) continue; // đã xử lý ở trên
            $basename = basename(parse_url($v, PHP_URL_PATH));
            // thử tìm theo file_name
            $img = ProductImage::where('product_id', $product->id)
                ->where('file_name', $basename)
                ->first();

            if ($img) {
                $full = public_path(trim($img->path, '/').'/'.$img->file_name);
                if (is_file($full)) @unlink($full);
                $img->delete();
            } else {
                // fallback: xoá file thẳng nếu tồn tại trong thư mục gallery
                $full = public_path('images/product/gallery/'.$basename);
                if (is_file($full)) @unlink($full);
            }
        }

        /* ============ CẬP NHẬT ALT / SORT ẢNH HIỆN CÓ ============ */
        $alts  = (array) $request->input('alt', []);
        $sorts = (array) $request->input('sort_order', []);
        foreach ($product->images as $img) {
            if (array_key_exists($img->id, $alts)) {
                $img->alt = $alts[$img->id];
            }
            if (array_key_exists($img->id, $sorts) && $sorts[$img->id] !== '') {
                $img->sort_order = (int) $sorts[$img->id];
            }
            $img->save();
        }

        /* ====================== THÊM ẢNH MỚI ====================== */
        $galleryPath = 'images/product/gallery';
        if (!is_dir(public_path($galleryPath))) {
            @mkdir(public_path($galleryPath), 0775, true);
        }

        $altsNew  = (array) $request->input('alt_new', []);
        $sortsNew = (array) $request->input('sort_new', []);
        $i = 0;

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if (!$file->isValid()) continue;
                $fname = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
                $file->move(public_path($galleryPath), $fname);

                ProductImage::create([
                    'product_id' => $product->id,
                    'file_name'  => $fname,
                    'path'       => $galleryPath,
                    'alt'        => $altsNew[$i] ?? null,
                    'sort_order' => is_numeric($sortsNew[$i] ?? null) ? (int) $sortsNew[$i] : 0,
                ]);
                $i++;
            }
        }

        return redirect()
            ->route('admin.product.edit', $product->id)
            ->with('success', 'Cập nhật sản phẩm thành công');
    }

    public function destroy($id)
    {
        $product = Product::with('images')->findOrFail($id);

        // Xoá toàn bộ ảnh gallery
        foreach ($product->images as $img) {
            $full = public_path(trim($img->path, '/').'/'.$img->file_name);
            if (is_file($full)) @unlink($full);
            $img->delete();
        }

        // Xoá ảnh chính
        if (!empty($product->image)) {
            $main = public_path('images/product/'.$product->image);
            if (is_file($main)) @unlink($main);
        }

        $product->delete();

        return redirect()->route('admin.product.index')->with('success', 'Đã xóa sản phẩm');
    }
}
