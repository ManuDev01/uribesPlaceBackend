<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\ShoppingCartService;

use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Patch;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Delete;

#[Prefix("/shoppingCart")]
class ShoppingCartController extends Controller
{

    protected $shoppingCart;

    public function __construct(ShoppingCartService $shoppingCart){
        $this->shoppingCart = $shoppingCart;
    }

    #[Get('/getShoppingCart/{userId}')]
    public function getShoppingCart($userId){
        return response()->json($this->shoppingCart->getShoppingCart($userId), 200);

    }

    #[Post('/saveProduct')]
    public function saveProduct($request){
    
    }

    #[Patch('/updateQuantity/{userId}/{productId}/{cantidad}')]
    public function updateQuantity($userId, $productId, $cantidad){
        return response()->json($this->shoppingCart->actualizarCantidad($userId, $productId, $cantidad), 200);
    }

    #[Delete('/deleteProduct/{userId}/{productId}')]
    public function deleteProduct($userId, $productId){
        return response()->json($this->shoppingCart->eliminarDelCarrito($userId, $productId), 200);
    }
}
