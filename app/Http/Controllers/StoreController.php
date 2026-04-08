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
        $user = $request->get('user');
        if (!$user) {
            return response()->json(['error' => 'No autorizado. Debe estar logueado.'], 401);
        }

        try {
            $id = $this->stores->create($request->all(), $user->userId);
            return response()->json([
                'message' => 'Tienda creada exitosamente',
                'storeId' => $id
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: 400);
        }
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

    #[Post('/rate')]
    public function rate(Request $request)
    {
        $request->validate([
            'idStore' => 'required|integer',
            'idUser' => 'required|integer',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $this->stores->rateStore(
            $request->idStore,
            $request->idUser,
            $request->rating
        );

        return response()->json(['message' => 'Rating updated successfully'], 200);
    }

    #[Post('/visit/{id}')]
    public function recordVisit(Request $request, $id)
    {
        // El idUser puede ser nulo si el visitante no está logueado
        $userId = $request->input('idUser');
        $ip = $request->ip();

        $this->stores->registerVisit($id, $userId, $ip);

        return response()->json([
            'status' => 'success',
            'message' => 'Visit registered in logs'
        ], 200);
    }

    #[Get('/statistics/most-visited')]
    public function mostVisited()
    {
        $stats = $this->stores->getMostVisited(5);
        return response()->json(['data' => $stats], 200);
    }

    #[Patch('/deactivate/{id}')]
    public function destroy($id) {
        $this->stores->deactivate($id);
        return response()->json(['message' => 'Store deactivated successfully'], 200);
    }
}
