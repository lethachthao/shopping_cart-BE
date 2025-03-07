<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class UserProductController extends Controller
{
    public function index()
    {
        $products = Product::with("category") // Lấy thông tin danh mục
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'description' => $product->description,
                    'image' => asset('storage/' . $product->image), // Hiển thị đường dẫn đầy đủ của ảnh
                    'category' => $product->category ? $product->category->name : null,
                ];
            });

        return response()->json($products, 200);
    }

    public function show($id)
    {
        $product = Product::with("category")->find($id);

        if (!$product) {
            return response()->json(['message' => 'Sản phẩm không tồn tại'], 404);
        }

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'description' => $product->description,
            'image' => asset('storage/' . $product->image), // Đường dẫn đầy đủ của ảnh
            'category' => $product->category ? $product->category->name : null,
        ], 200);
    }
}
