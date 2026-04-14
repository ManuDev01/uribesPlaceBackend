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
            return DB::select("SELECT count(u.userId) as registradosHoy from users u where DATE(createAt) = CURDATE() and isActive = 1");
        }

public function activityLog() {
    return DB::select("SELECT 
            CONCAT(u.firstName, ' ', u.lastName) as nombre,
            al.activityDescription,
            al.createdAt
        FROM activitylog al 
        LEFT JOIN users u ON al.userId = u.userId
        ORDER BY al.createdAt DESC");
}

        public function getTotalVisitas(){
            return DB::select("select s.storeName, count(s.storeName) as cantidad 
                                from store_visits sv 
                                join store s on sv.idStore = s.storeId 
                                order by cantidad DESC");
        }

        public function getActivityByHour() {
    return DB::select("SELECT 
            HOUR(createdAt) as hora, 
            COUNT(*) as total_visitas
        FROM activitylog
        GROUP BY HOUR(createdAt)
        ORDER BY hora ASC");
}

public function cambiarRolUsuario($userId, $nuevoRol, $adminId) {
    // 1. Obtenemos el nombre del administrador y del usuario afectado
    // Usamos un JOIN o dos consultas rápidas para armar la descripción
    $datos = DB::selectOne("
        SELECT 
            (SELECT nickname FROM users WHERE userId = ?) as adminName,
            (SELECT nickname FROM users WHERE userId = ?) as targetName
    ", [$adminId, $userId]);

    $descripcion = "El administrador {$datos->adminName} cambió el rol de {$datos->targetName} a {$nuevoRol}";

    // 2. Insertamos en el log de actividad usando el ID del administrador
    DB::insert("
        INSERT INTO activitylog (userId, activityDescription, createdAt) 
        VALUES (?, ?, NOW())
    ", [$adminId, $descripcion]);

    // 3. Actualizamos el rol
    return DB::update("UPDATE users SET role = ? WHERE userId = ?", [$nuevoRol, $userId]);
}

public function searchUser($searchTerm) 
{
    // Preparamos el término para que busque coincidencias parciales
    $queryTerm = '%' . $searchTerm . '%';

    return DB::select("SELECT 
            u.userId, 
            u.nickname, 
            u.firstName, 
            u.lastName, 
            u.email, 
            u.role, 
            u.isActive, 
            u.DNI, 
            s.stateName, 
            m.municipalityName, 
            u.address, 
            u.zipCode, 
            u.phoneNumber, 
            u.ImageUrl
        FROM users u
        LEFT JOIN state s ON u.stateId = s.stateId
        LEFT JOIN municipalities m ON u.municipalitiesId = m.municipalitiesId
        WHERE u.nickname LIKE ? 
           OR u.firstName LIKE ? 
           OR u.lastName LIKE ? 
           OR u.email LIKE ?", 
        [$queryTerm, $queryTerm, $queryTerm, $queryTerm]);
}

    }