<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ProductService {

    public function listAll(){
        return DB::select("SELECT * FROM products");
    }

    public function create($data){
        return DB::insert("INSERT INTO products
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
    }

    public function findById($id){
        return DB::select("SELECT * FROM products WHERE idProduct = ?", [$id]);
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
        return DB::delete("DELETE FROM products WHERE idProduct = ?", [$id]);
    }
}
