<?php

    namespace App\Http\Controllers;

    use App\Services\AdminService;
    use App\Http\Controllers\Controller;

    use Illuminate\Http\Request;

    class AdminController extends Controller {
        protected $adminService;

        public function __construct(AdminService $adminService) {
            $this->adminService = $adminService;
        }

    }