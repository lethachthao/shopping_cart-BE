<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banner = Banner::all()->map(function ($banner){
            return [
                'id' => $banner->id,
                'image' => $banner->image,
                'title' => $banner->title,
            ];
        } );

        return response()->json($banner, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'title' => 'nullable|string'
    ]);

    if ($request->hasFile('image')) {
        $validatedData['image'] = $request->file('image')->store('banners', 'public');
    }

    $banner = Banner::create($validatedData);

    return response()->json($banner, 201);
}



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
        // Cập nhật banner
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id); // Tự động trả về 404 nếu không tìm thấy

        $validatedData = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'nullable|string'
        ]);

        if ($request->hasFile('image')) {
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image); // Xóa ảnh cũ nếu có
            }
            $validatedData['image'] = $request->file('image')->store('banners', 'public');
        } else {
            $validatedData['image'] = $banner->image;
        }

        $banner->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Banner updated successfully!',
            'data' => $banner,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $banner = Banner::find($id);
        if (!$banner) {
            return response()->json(['message' => 'Banner not found'], 404);
        }

        Storage::disk('public')->delete($banner->image);
        $banner->delete();

        return response()->json(['message' => 'Banner deleted'], 200);
    }
}
