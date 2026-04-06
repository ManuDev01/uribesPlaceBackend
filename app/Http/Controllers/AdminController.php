<?php

    namespace App\Http\Controllers;

    use App\Services\AdminService;
    use App\Http\Controllers\Controller;

    use Illuminate\Http\Request;

    use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Patch;
use Spatie\RouteAttributes\Attributes\Delete;
use Spatie\RouteAttributes\Attributes\Prefix;

    #[Prefix('/admin')]
    class AdminController extends Controller {
        protected $adminService;

        public function __construct(AdminService $adminService) {
            $this->adminService = $adminService;
        }

        #[Get('/cantidadTiendas')]
        function getCantidadTiendas(){
            return response()->json($this->adminService->getCantidadTiendas(), 200);
        }

        #[Get('/tiendasInactivas')]
        function getCantidadTiendasInactiva(){
            return response()->json($this->adminService->getCantidadTiendasInactivas(), 200);
        }

        # TODO: Crear endpoint para obtener los usuarios que se registraron nuevos con el create_at

        #[Get('/totalUsuarios')]
        function getTotalUsuarios(){
            return response()->json($this->adminService->getTotalUsuarios(), 200);
        }

    }