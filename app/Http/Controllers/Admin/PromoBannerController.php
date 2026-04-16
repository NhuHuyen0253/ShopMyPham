<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoBanner;
use App\Models\Promotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PromoBannerController extends Controller
{
    public function index()
    {
        $banners = PromoBanner::with('promotion')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.promo_banners.index', compact('banners'));
    }

    public function create()
    {
        $promotions = Promotion::orderByDesc('id')->get();

        return view('admin.promo_banners.create', compact('promotions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'promotion_id'   => 'nullable|exists:promotions,id',
            'name'           => 'required|string|max:255',
            'headline'       => 'required|string|max:255',
            'subheadline'    => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'discount_text'  => 'nullable|string|max:255',
            'button_text'    => 'nullable|string|max:100',
            'button_link'    => 'nullable|string|max:255',
            'image'          => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'start_at'       => 'nullable|date',
            'end_at'         => 'nullable|date|after_or_equal:start_at',
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'nullable',
        ], [
            'headline.required' => 'Vui lòng nhập tiêu đề chính.',
            'end_at.after_or_equal' => 'Thời gian kết thúc phải lớn hơn hoặc bằng thời gian bắt đầu.',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('promo_banners', 'public');
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = $request->has('is_active');

        PromoBanner::create($data);

        return redirect()
            ->route('admin.promo-banners.index')
            ->with('success', 'Tạo banner khuyến mãi thành công!');
    }

    public function edit(PromoBanner $promo_banner)
    {
        $banner = $promo_banner;
        $promotions = Promotion::orderByDesc('id')->get();

        return view('admin.promo_banners.edit', compact('banner', 'promotions'));
    }

    public function update(Request $request, PromoBanner $promo_banner)
    {
        $data = $request->validate([
            'promotion_id'   => 'nullable|exists:promotions,id',
            'name'           => 'required|string|max:255',
            'headline'       => 'required|string|max:255',
            'subheadline'    => 'nullable|string|max:255',
            'description'    => 'nullable|string',
            'discount_text'  => 'nullable|string|max:255',
            'button_text'    => 'nullable|string|max:100',
            'button_link'    => 'nullable|string|max:255',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'start_at'       => 'nullable|date',
            'end_at'         => 'nullable|date|after_or_equal:start_at',
            'sort_order'     => 'nullable|integer|min:0',
            'is_active'      => 'nullable',
        ], [
            'headline.required' => 'Vui lòng nhập tiêu đề chính.',
            'end_at.after_or_equal' => 'Thời gian kết thúc phải lớn hơn hoặc bằng thời gian bắt đầu.',
        ]);

        if ($request->hasFile('image')) {
            if ($promo_banner->image && Storage::disk('public')->exists($promo_banner->image)) {
                Storage::disk('public')->delete($promo_banner->image);
            }

            $data['image'] = $request->file('image')->store('promo_banners', 'public');
        }

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = $request->has('is_active');

        $promo_banner->update($data);

        return redirect()
            ->route('admin.promo-banners.index')
            ->with('success', 'Cập nhật banner thành công!');
    }

    public function destroy(PromoBanner $promo_banner)
    {
        if ($promo_banner->image && Storage::disk('public')->exists($promo_banner->image)) {
            Storage::disk('public')->delete($promo_banner->image);
        }

        $promo_banner->delete();

        return redirect()
            ->route('admin.promo-banners.index')
            ->with('success', 'Đã xóa banner.');
    }
}