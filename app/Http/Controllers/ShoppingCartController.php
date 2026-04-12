<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Service\ShoppingCartService;

use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Patch;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Prefix("/shoppingCart")]
class ShoppingCartController extends Controller
{

    protected $shoppingCart;

    public function __construct(ShoppingCartService $shoppingCart){
        $this->shoppingCart = $shoppingCart;
    }

    #[Get('/getShoppingCart')]
    public function getShoppingCart($userId){

    }

    #[Post('/saveProduct')]
    public function saveProduct($request){
    
    }
}
