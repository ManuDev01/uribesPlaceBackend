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
            return response()->json(['data' => $this->adminService->getCantidadTiendas()], 200);
        }

        

    }