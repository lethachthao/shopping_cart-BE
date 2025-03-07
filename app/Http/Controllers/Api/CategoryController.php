<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Category = Category::all()->map(function ($Category){
            return [
                'id' => $Category->id,
                'name' => $Category->name,
                'logo' => $Category->logo,
                'banner' => $Category->banner,
            ];
        });
        return response()->json( $Category, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $ValidatedData = $request->validate([
            'name' => 'required|string',
            'logo' => 'file|mimes:jpeg,png,jpg,gif',
            'banner' => 'file|mimes:jpeg,png,jpg,gif'
        ]);

        if ($request->hasFile('banner')) {
            $ValidatedData['banner'] = $request->file('banner')->store('categories', 'public');
        }

        if ($request->hasFile('logo')) {
            $ValidatedData['logo'] = $request->file('logo')->store('categories', 'public');
        }

        $category = Category::create($ValidatedData);
        return response()->json($category, 201);
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
    public function update(Request $request, string $id)
    {
        $category = Category::findOrFail($id);

        $ValidatedData = $request->validate([
            'name' => 'required|string',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,gif',
            'banner' => 'nullable|file|mimes:jpeg,png,jpg,gif'
        ]);

        // Cập nhật tên category
        $category->name = $ValidatedData['name'];

        // Xử lý logo nếu có upload mới
        if ($request->hasFile('logo')) {
            // Xóa ảnh cũ nếu có
            if ($category->logo) {
                Storage::disk('public')->delete($category->logo);
            }
            // Lưu ảnh mới
            $category->logo = $request->file('logo')->store('categories', 'public');
        }

        // Xử lý banner nếu có upload mới
        if ($request->hasFile('banner')) {
            // Xóa ảnh cũ nếu có
            if ($category->banner) {
                Storage::disk('public')->delete($category->banner);
            }
            // Lưu ảnh mới
            $category->banner = $request->file('banner')->store('categories', 'public');
        }

        // Lưu cập nhật vào database
        $category->save();

        return response()->json($category, 200);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        Storage::delete([$category->banner, $category->logo]);
        $category->delete();

        return response()->json(['message' => 'Category deleted'], 200);
    }
}
