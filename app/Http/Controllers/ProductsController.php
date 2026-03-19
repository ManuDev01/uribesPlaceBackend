<?php

namespace App\Http\Controllers;

use App\Services\ProductsService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller {
    protected $products;

    public function __construct(ProductsService $productService) {
        $this->products = $productService;
    }

    public function getAll(){
        $allProducts = $this->products->listAll();
        return response()->json(['data' => $allProducts], 200);
    }

    public function store(Request $request){
        $this->products->create($request->all());
        return response()->json(['data' => 'Product created successfully'], 201);
    }

    public function show($id){
        $product = $this->products->findById($id);
        return response()->json(['data' => $product], 200);
    }

    public function update(Request $request, $id){
        $this->products->update($id, $request->all());
        return response()->json(['data' => 'Product updated successfully'], 200);
    }

    public function destroy($id){
        $this->products->remove($id);
        return response()->json(['data' => 'Product deleted successfully'], 200);
    }
}
