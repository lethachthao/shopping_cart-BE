<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $product = Product::with("category")->get()->map(function ($product){
            return [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'description' => $product->description,
                'image' => $product->image,
                'category' => $product->category ? $product->category->name : null,
            ];
        });
        return response()->json($product, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable',
            'description' => 'string',
            'category_id' => 'nullable|exists:categories,id', // Đảm bảo category tồn tại
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',
        ]);

        // Xử lý upload ảnh chính (image)
        if ($request->hasFile('image')) {
            $validatedData['image'] = $request->file('image')->store('products', 'public');
        }

        // Tạo sản phẩm
        $product = Product::create($validatedData);

        return response()->json([
            'message' => 'Sản phẩm đã được tạo thành công!',
            'product' => $product
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        // Xác thực dữ liệu đầu vào
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'nullable',
            'description' => 'string',
            'category_id' => 'nullable|exists:categories,id', // Đảm bảo category tồn tại
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image); // Xóa ảnh cũ nếu có
            }
            $validatedData['image'] = $request->file('image')->store('products', 'public');
        } else {
            $validatedData['image'] = $product->image;
        }

        $product->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'product updated successfully!',
            'data' => $product,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'product not found'], 404);
        }

        Storage::disk('public')->delete($product->image);
        $product->delete();

        return response()->json(['message' => 'product deleted'], 200);
    }




}
