<?php

    namespace App\Services;
    
    use Illuminate\Support\Facades\DB;

    class ShoppingCartService {


    function agregarAlCarrito($userId, $productId, $cantidad) {
        // Verificar si el producto ya está en el carrito del usuario
        $carritoExistente = DB::select("SELECT * FROM shopping_cart WHERE userId = ? AND productId = ?", [$userId, $productId]);

        if ($carritoExistente) {
            // Si el producto ya está en el carrito, actualizar la cantidad
            DB::update("UPDATE shopping_cart SET quantity = quantity + ? WHERE userId = ? AND productId = ?", [$cantidad, $userId, $productId]);
        } else {
            // Si el producto no está en el carrito, agregarlo
            DB::insert("INSERT INTO shopping_cart (userId, productId, quantity) VALUES (?, ?, ?)", [$userId, $productId, $cantidad]);
        }

        return ['message' => 'Producto agregado al carrito exitosamente'];
    }
    }