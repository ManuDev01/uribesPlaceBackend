<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService {

    public function listAll(){
        // Traemos productos y una imagen principal de referencia
        return DB::select("SELECT p.*, (SELECT imageUrl FROM product_images WHERE idProduct = p.idProduct LIMIT 1) as mainImage FROM products p");
    }

    public function create($data, $base64Images = []){
        // 1. Tu INSERT original de productos
        DB::insert("INSERT INTO products
            (productName, productDescription, brand, price, idStore, idProductQuality, stock, SKU, createAt, modifiedAt)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $data['productName'],
                $data['productDescription'],
                $data['brand'],
                $data['price'],
                $data['idStore'],
                $data['idProductQuality'],
                $data['stock'],
                $data['SKU']
            ]);

        $productId = DB::getPdo()->lastInsertId();

        // 2. Procesar las imágenes Base64
        if (!empty($base64Images)) {
            foreach ($base64Images as $base64String) {
                // Extraer el contenido puro del base64 (quitando el encabezado data:image/...)
                $image_service_str = substr($base64String, strpos($base64String, ",") + 1);
                $image_data = base64_decode($image_service_str);

                // Generar nombre único para el archivo
                $fileName = 'prod_' . time() . '_' . Str::random(10) . '.png';
                $path = 'products/' . $fileName;

                // Guardar el archivo físicamente en storage/app/public/products
                Storage::disk('public')->put($path, $image_data);

                // Guardar la ruta en la tabla de imágenes
                DB::insert("INSERT INTO product_images (idProduct, imageUrl) VALUES (?, ?)", [
                    $productId,
                    $path
                ]);
            }
        }

        return $productId;
    }

    public function findById($id){
        $product = DB::select("SELECT * FROM products WHERE idProduct = ?", [$id]);
        if (!empty($product)) {
            // Añadir las imágenes al objeto del producto
            $product[0]->images = DB::select("SELECT imageUrl FROM product_images WHERE idProduct = ?", [$id]);
        }
        return $product;
    }

    public function update($id, $data){
        return DB::update("UPDATE products SET
            productName = ?, productDescription = ?, brand = ?, price = ?, stock = ?, modifiedAt = NOW()
            WHERE idProduct = ?",
            [
                $data['productName'],
                $data['productDescription'],
                $data['brand'],
                $data['price'],
                $data['stock'],
                $id
            ]);
    }

    public function remove($id){
        // Primero buscamos las rutas para borrar los archivos físicos
        $images = DB::select("SELECT imageUrl FROM product_images WHERE idProduct = ?", [$id]);
        foreach ($images as $img) {
            Storage::disk('public')->delete($img->imageUrl);
        }
            //desactivamos el producto (soft delete)
            DB::update("UPDATE products set isActive = 0 WHERE idProduct = ?", [$id]);

            return true;
    }
}
