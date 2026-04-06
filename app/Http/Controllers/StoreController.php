<?php

namespace App\Http\Controllers;

use App\Services\StoreService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Patch;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Prefix('stores')]
class StoreController extends Controller
{
    protected $stores;

    public function __construct(StoreService $storeService) {
        $this->stores = $storeService;
    }

    #[Get('/getAllStores')]
    public function index() {
        $allStores = $this->stores->listAllActive();
        return response()->json(['data' => $allStores], 200);
    }

    #[Post('/create')]
    public function store(Request $request) {
        $id = $this->stores->create($request->all());
        return response()->json(['message' => 'Store created successfully', 'storeId' => $id], 201);
    }

    #[Get('/owner/{idOwner}')]
    public function showByOwner($idOwner) {
        $store = $this->stores->findByOwner($idOwner);
        return response()->json(['data' => $store], 200);
    }

    #[Patch('/update/{id}')]
    public function update(Request $request, $id) {
        $this->stores->update($id, $request->all());
        return response()->json(['message' => 'Store updated successfully'], 200);
    }

    #[Patch('/deactivate/{id}')]
    public function destroy($id) {
        $this->stores->deactivate($id);
        return response()->json(['message' => 'Store deactivated successfully'], 200);
    }
}
