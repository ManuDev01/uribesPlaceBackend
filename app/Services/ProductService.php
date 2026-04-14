<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService {

    public function listAll(){
        return DB::select("
            SELECT p.*,
            (SELECT imageUrl FROM product_images WHERE idProduct = p.idProduct AND isActive = 1 LIMIT 1) as mainImage
            FROM products p
            WHERE p.isActive = 1
        ");
    }

    public function create($data){

        $imageUrl = null;
        if (isset($data['imageUrl']) && !empty($data['imageUrl'])) {
            $image_service_str = substr($data['imageUrl'], strpos($data['imageUrl'], ",") + 1);
            $image_data = base64_decode($image_service_str);
            $fileName = 'product_' . time() . '_' . \Illuminate\Support\Str::random(5) . '.png';
            $imageUrl = 'products/' . $fileName;
            \Illuminate\Support\Facades\Storage::disk('public')->put($imageUrl, $image_data);
        }

        DB::insert("INSERT INTO products
            (productName, productDescription, brand, price, idStore, idProductQuality, stock, SKU, isActive, createAt, modifiedAt,idCategory, idSubcategory, ImageUrl)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW(), ?, ?, ?)",
            [
                $data['productName'],
                $data['productDescription'],
                $data['brand'],
                $data['price'],
                $data['idStore'],
                $data['idProductQuality'],
                $data['stock'],
                $data['SKU'],
                $data['idCategory'],
                $data['idSubcategory'],
                $imageUrl
            ]);

        $productId = DB::getPdo()->lastInsertId();
        return $productId;
    }

    public function findById($id){
        $product = DB::select("SELECT * FROM products WHERE idProduct = ?", [$id]);
        if (!empty($product)) {
            $product[0]->images = DB::select("SELECT imageUrl FROM product_images WHERE idProduct = ? AND isActive = 1", [$id]);
        }
        return $product;
    }

    //esta vaina la voy a dejar asi mentras tanto
    // public function findByIdStore($id){
     //  return DB::select("select s.storeId, s.storeName, s.ImageUrl as storeImage, p.idProduct, p.productName, p.price,
     //  (SELECT imageUrl FROM product_images WHERE idProduct = p.idProduct AND isActive = 1 LIMIT 1) as mainImage
     //  FROM products p
     //  JOIN store s ON p.idStore = s.storeId
     //  WHERE p.idStore = ? AND p.isActive = 1", [$id]);
    //}

    public function update($id, $data) {
        $fields = "";
        $values = [];

        foreach ($data as $key => $value) {
            if ($key !== 'idProduct' && $key !== '_method' && $key !== 'images') {
                $fields .= "$key = ?, ";
                $values[] = $value;
            }
        }

        $fields .= "modifiedAt = NOW()";
        $values[] = $id;

        return DB::update("UPDATE products SET $fields WHERE idProduct = ?", $values);
    }

    // Implementación de Soft Delete mediante PATCH
    public function remove($id){
        // Desactivamos el producto
        DB::update("UPDATE products SET isActive = 0, modifiedAt = NOW() WHERE idProduct = ?", [$id]);

        // Desactivamos sus imágenes para mantener consistencia
        return DB::update("UPDATE product_images SET isActive = 0 WHERE idProduct = ?", [$id]);
    }

}
