<?php

namespace App\Http\Controllers;

use App\Services\StoreService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; // Añadido
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
        // Obtenemos el usuario autenticado desde el token JWT
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'No autorizado. Debe estar logueado.'], 401);
        }

        try {
            // Usamos userId que es como lo tienes en tu tabla de usuarios
            $id = $this->stores->create($request->all(), $user->userId);
            return response()->json([
                'message' => 'Tienda creada exitosamente',
                'storeId' => $id
            ], 201);
        } catch (\Throwable $e) { // <-- IMPORTANTE: \Throwable captura el error de tipo que te sale
        return response()->json([
            'error' => 'Error en la operación',
            'detalle' => $e->getMessage() // Aquí verás el error REAL de la BD
        ], 500); // <-- Número entero fijo para que no falle
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
            'rating' => 'required|integer|min:1|max:5',
        ]);

        // Obtenemos el usuario del token para calificar
        $userId = Auth::id();

        $this->stores->rateStore(
            $request->idStore,
            $userId,
            $request->rating
        );

        return response()->json(['message' => 'Rating updated successfully'], 200);
    }

    #[Post('/visit/{id}')]
    public function recordVisit(Request $request, $id)
    {
        // El idUser puede ser nulo si el visitante no está logueado,
        // pero si hay token, lo capturamos automáticamente
        $userId = Auth::id();
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
