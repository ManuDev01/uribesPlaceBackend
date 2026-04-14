<?php

    namespace App\Services;
    
    use Illuminate\Support\Facades\DB;

    class ShoppingCartService {

    function saveProduct($request){
        DB::insert("INSERT INTO shoppingCart (userId, productId, quantity) VALUES (?, ?, ?)", [$request->userId, $request->productId, $request->quantity]);
        return ['message' => 'Producto agregado al carrito exitosamente'];
    }

    function getShoppingCart($userId) {
            return DB::select("SELECT sc.cartId, sc.productId, sc.quantity,p.price, p.productName, p.productDescription, s.storeName FROM shoppingCart sc JOIN products p ON sc.productId = p.idProduct JOIN store s ON p.idStore = s.storeId WHERE sc.userId = ?", [$userId]);
        }


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

        function actualizarCantidad($userId, $productId, $cantidad) {
            DB::update("UPDATE shoppingCart SET quantity = ? WHERE userId = ? AND productId = ?", [$cantidad, $userId, $productId]);
            return ['message' => 'Cantidad actualizada exitosamente'];
        }

        function eliminarDelCarrito($userId, $productId) {
            DB::delete("DELETE FROM shoppingCart WHERE userId = ? AND productId = ?", [$userId, $productId]);
            return ['message' => 'Producto eliminado del carrito exitosamente'];
        }
    }