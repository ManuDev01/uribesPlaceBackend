<?php

    namespace App\Services;

    use Illuminate\Support\Facades\DB;

    class AdminService {

    public $inicioPrimavera = "20/03";
    public $finPrimavera = "21/06";
    public $inicioVerano = "21/06";
    public $finVerano = "22/09";
    public $inicioOtono = "22/09";
    public $finOtono = "21/12";
    public $inicioInvierno = "21/12";
    public $finInvierno = "20/03";

        public function listAllAdmins() {

        }

        public function getCantidadTiendas(){
            return DB::select("SELECT COUNT(s.storeId) from stores s where storeIsActive = true");
        }

        
    }