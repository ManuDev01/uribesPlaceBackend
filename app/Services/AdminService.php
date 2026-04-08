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

        public function getCantidadTiendas(){
            return DB::select("SELECT COUNT(s.storeId) as totalTiendas from store s where storeIsActive = true");
        }

        public function getCantidadTiendasInactivas(){
            return DB::select("Select COUNT(s.storeId) as totalTiendasInactivas from store s where storeIsActive = false");
        }

        public function getTotalUsuarios(){
            return DB::select("SELECT count(u.userId) as totalUsuarios from users u where isActive = 1");
        }

        public function getUsuarioRegistradosHoy(){
            return DB::select("SELECT count(u.userId) from users where DATE(createAt) = CURDATE() and isActive = 1");
        }

        public function activityLog(){
            return DB::select("SELECT CONCAT(u.firstName, ' ', u.lastName) as nombre,
                        activityDescription,
                        createdAt
                        from activitylog al join users u on al.userId = u.userId");
        }

        public function getTotalVisitas(){
            return DB::select("select s.storeName, count(s.storeName) as cantidad 
                                from store_visits sv 
                                join store s on sv.idStore = s.storeId 
                                order by cantidad DESC");
        }


    }
