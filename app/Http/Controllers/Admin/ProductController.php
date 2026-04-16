<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Brands;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /* LIST */
    public function index(Request $request)
    {
        $q = $request->get('q');

        $products = Product::with(['brand', 'category'])
            ->when($q, function ($qr) use ($q) {
                $qr->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%")
                        ->orWhere('group_code', 'like', "%{$q}%")
                        ->orWhere('capacity', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.product.index', compact('products', 'q'));
    }

    /* CREATE FORM */
    public function create()
    {
        $categories = Category::all();
        $brands = Brands::query()
            ->orderByRaw('name COLLATE utf8mb4_unicode_520_ci')
            ->get();

        return view('admin.product.create', compact('categories', 'brands'));
    }

    /* STORE */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'sku'                => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'group_code'         => ['nullable', 'string', 'max:255'],
            'capacity'           => ['nullable', 'string', 'max:50'],
            'description'        => ['nullable', 'string'],
            'usage_instructions' => ['nullable', 'string'],
            'price'              => ['required', 'integer', 'min:0'],
            'original_price'     => ['nullable', 'numeric', 'min:0'],
            'discount_percent'   => ['nullable', 'integer', 'min:0', 'max:100'],
            'quantity'           => ['nullable', 'integer', 'min:0'],
            'brand_id'           => ['required', 'integer'],
            'category_id'        => ['required', 'integer'],
            'is_hotdeal'         => ['nullable', 'boolean'],
            'image'              => ['nullable', 'image', 'max:5120'],
            'images.*'           => ['nullable', 'image', 'max:5120'],
            'images_alt.*'       => ['nullable', 'string', 'max:255'],
            'images_sort.*'      => ['nullable', 'integer', 'min:0'],
        ], [
            'sku.unique' => 'SKU đã tồn tại.',
        ]);

        DB::transaction(function () use ($request, &$data) {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imageName = time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/product'), $imageName);
                $data['image'] = $imageName;
            }

            $data['sku'] = !empty($data['sku']) ? trim($data['sku']) : null;
            $data['group_code'] = !empty($data['group_code']) ? Str::slug(trim($data['group_code'])) : null;
            $data['capacity'] = !empty($data['capacity']) ? trim($data['capacity']) : null;
            $data['is_hotdeal'] = (bool) ($data['is_hotdeal'] ?? false);

            // Khi tạo mới, nếu không nhập quantity thì mặc định 0
            $data['quantity'] = isset($data['quantity']) ? (int) $data['quantity'] : 0;

            if (!$data['is_hotdeal']) {
                $data['discount_percent'] = null;
            }

            /** @var Product $product */
            $product = Product::create($data);

            if ($request->hasFile('images')) {
                $dir = 'product_images/' . $product->id;
                $alts = $request->input('images_alt', []);
                $sorts = $request->input('images_sort', []);

                foreach ($request->file('images') as $i => $file) {
                    if (!$file) {
                        continue;
                    }

                    $stored = $file->store($dir, 'public');
                    $stored = str_replace('\\', '/', $stored);

                    ProductImage::create([
                        'product_id' => $product->id,
                        'file_name'  => basename($stored),
                        'path'       => $dir,
                        'alt'        => $alts[$i] ?? null,
                        'sort_order' => $sorts[$i] ?? ($i + 1),
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.product.index')
            ->with('success', 'Tạo sản phẩm thành công!');
    }

    /* EDIT FORM */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $brands = Brands::query()
            ->orderByRaw('name COLLATE utf8mb4_unicode_520_ci')
            ->get();

        $product->load('images');

        return view('admin.product.edit', compact('product', 'categories', 'brands'));
    }

    /* UPDATE */
    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'sku'                => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($product->id)],
            'group_code'         => ['nullable', 'string', 'max:255'],
            'capacity'           => ['nullable', 'string', 'max:50'],
            'description'        => ['nullable', 'string'],
            'usage_instructions' => ['nullable', 'string'],
            'price'              => ['required', 'integer', 'min:0'],
            'original_price'     => ['nullable', 'numeric', 'min:0'],
            'discount_percent'   => ['nullable', 'integer', 'min:0', 'max:100'],
            'quantity'           => ['nullable', 'integer', 'min:0'],
            'brand_id'           => ['required', 'integer'],
            'category_id'        => ['required', 'integer'],
            'is_hotdeal'         => ['nullable', 'boolean'],
            'image'              => ['nullable', 'image', 'max:5120'],
            'remove_image'       => ['nullable', 'boolean'],
            'images.*'           => ['nullable', 'image', 'max:5120'],
            'images_alt.*'       => ['nullable', 'string', 'max:255'],
            'images_sort.*'      => ['nullable', 'integer', 'min:0'],
            'gallery_alt.*'      => ['nullable', 'string', 'max:255'],
            'gallery_sort.*'     => ['nullable', 'integer', 'min:0'],
            'remove_gallery.*'   => ['nullable', 'boolean'],
        ], [
            'sku.unique' => 'SKU đã tồn tại.',
        ]);

        DB::transaction(function () use ($request, $product, &$data) {
            if ($request->boolean('remove_image') && $product->image) {
                @unlink(public_path('images/product/' . $product->image));
                $product->image = null;
            }

            if ($request->hasFile('image')) {
                if ($product->image) {
                    @unlink(public_path('images/product/' . $product->image));
                }

                $file = $request->file('image');
                $imageName = time() . '_' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('images/product'), $imageName);
                $data['image'] = $imageName;
            }

            $data['sku'] = !empty($data['sku']) ? trim($data['sku']) : null;
            $data['group_code'] = !empty($data['group_code']) ? Str::slug(trim($data['group_code'])) : null;
            $data['capacity'] = !empty($data['capacity']) ? trim($data['capacity']) : null;
            $data['is_hotdeal'] = (bool) ($data['is_hotdeal'] ?? false);

            if (!$data['is_hotdeal']) {
                $data['discount_percent'] = null;
            }

            // Quan trọng:
            // Không cho màn sửa sản phẩm ghi đè tồn kho.
            // Tồn kho chỉ cập nhật ở màn nhập/xuất kho.
            unset($data['quantity']);

            $product->update($data);

            $idsToDelete = array_keys($request->input('remove_gallery', []));
            if (!empty($idsToDelete)) {
                $imagesToDelete = ProductImage::where('product_id', $product->id)
                    ->whereIn('id', $idsToDelete)
                    ->get();

                foreach ($imagesToDelete as $img) {
                    if (method_exists($img, 'deletePhysicalFile')) {
                        $img->deletePhysicalFile();
                    }
                    $img->delete();
                }
            }

            $galleryAlt = $request->input('gallery_alt', []);
            $gallerySort = $request->input('gallery_sort', []);

            if (!empty($galleryAlt) || !empty($gallerySort)) {
                $imagesRemain = ProductImage::where('product_id', $product->id)->get();

                foreach ($imagesRemain as $img) {
                    if (array_key_exists($img->id, $galleryAlt)) {
                        $img->alt = $galleryAlt[$img->id];
                    }

                    if (array_key_exists($img->id, $gallerySort)) {
                        $img->sort_order = $gallerySort[$img->id];
                    }

                    $img->save();
                }
            }

            if ($request->hasFile('images')) {
                $dir = 'product_images/' . $product->id;
                $alts = $request->input('images_alt', []);
                $sorts = $request->input('images_sort', []);

                foreach ($request->file('images') as $i => $file) {
                    if (!$file) {
                        continue;
                    }

                    $stored = $file->store($dir, 'public');
                    $stored = str_replace('\\', '/', $stored);

                    ProductImage::create([
                        'product_id' => $product->id,
                        'file_name'  => basename($stored),
                        'path'       => $dir,
                        'alt'        => $alts[$i] ?? null,
                        'sort_order' => $sorts[$i] ?? ($i + 1),
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.product.index')
            ->with('success', 'Cập nhật sản phẩm thành công!');
    }

    /* DELETE PRODUCT */
    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            $product->load('images');

            foreach ($product->images as $img) {
                if (method_exists($img, 'deletePhysicalFile')) {
                    $img->deletePhysicalFile();
                }
                $img->delete();
            }

            if ($product->image) {
                @unlink(public_path('images/product/' . $product->image));
            }

            $product->delete();
        });

        return redirect()
            ->route('admin.product.index')
            ->with('success', 'Đã xóa sản phẩm.');
    }

    /* DELETE ONE IMAGE (AJAX/FORM) */
    public function destroyImage(Request $request, ProductImage $image)
    {
        if (
            $request->filled('product_id') &&
            (int) $request->input('product_id') !== (int) $image->product_id
        ) {
            return $request->expectsJson()
                ? response()->json(
                    ['ok' => false, 'message' => 'Ảnh không thuộc sản phẩm.'],
                    422,
                    [],
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                )
                : back()->with('error', 'Ảnh không thuộc sản phẩm.');
        }

        $productId = $image->product_id;

        if (method_exists($image, 'deletePhysicalFile')) {
            $image->deletePhysicalFile();
        }
        $image->delete();

        if ($request->expectsJson()) {
            return response()->json(
                ['ok' => true, 'id' => (int) $request->input('image_id')],
                200,
                [],
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        }

        return redirect()
            ->route('admin.product.edit', $productId)
            ->with('success', 'Đã xóa ảnh.');
    }
}