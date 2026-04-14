<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Patch;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Prefix('products')]
class ProductController extends Controller
{
    protected $products;

    public function __construct(ProductService $productService)
    {
        $this->products = $productService;
    }

    #[Get('/getAllProducts')]
    public function getAll()
    {
        $allProducts = $this->products->listAll();
        return response()->json(['data' => $allProducts], 200);
    }

    #[Post('/create')]
    public function store(Request $request)
    {
        $data = $request->all();
        $images = $request->input('images', []);

        $this->products->create($data, $images);
        return response()->json(['data' => 'Product created successfully'], 201);
    }

    #[Get('/{id}')]
    public function show($id)
    {
        $product = $this->products->findById($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        return response()->json(['data' => $product], 200);
    }

    #[Patch('/update/{id}')]
    public function update(Request $request)
    {
        $id = $request->route('id');
        $data = $request->all();

        $result = $this->products->update($id, $data);

        if ($result) {
            return response()->json(['message' => 'Product updated successfully'], 200);
        }
        return response()->json(['error' => 'Product not found or no changes made'], 404);
    }

    // Cambiado de #[Delete] a #[Patch] para Soft Delete profesional
    #[Patch('/desactivate/{id}')]
    public function remove(Request $request)
    {
        $id = $request->route('id');
        $this->products->remove($id);

        return response()->json(['message' => 'Product and related images deactivated successfully'], 200);
    }
}
