<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function listAll()
    {
        return DB::select("SELECT userId, nickname, firstName, lastName, email, u.role, u.isActive, u.DNI, s.stateName, m.municipalityName, u.address, u.zipCode, u.phoneNumber, u.ImageUrl
                            FROM users u
                            left join state s on u.stateId = s.stateId
                            left join municipalities m on u.municipalitiesId = m.municipalitiesId");
    }

    public function register($userData)
    {
        // Mapeamos los datos del JSON a las columnas reales de tu SQL
        $nickname = $userData['userName']; // Usamos userName del JSON para el nickname
        $firstName = $userData['firstName'] ?? 'Sin Nombre';
        $email = $userData['userEmail'];
        $password = Hash::make($userData['userPassword']);
        $dni = $userData['dni'] ?? '00000000'; // Tu SQL pide DNI como not null

        // INSERT con los nombres de columna exactos de tu uribesPlaceDB.sql
        DB::insert(
            "INSERT INTO users (nickname, firstName, email, password, DNI)
                    VALUES (?, ?, ?, ?, ?)",
            [$nickname, $firstName, $email, $password, $dni]
        );

        DB::insert("INSERT into activitylog(activityDescription)
                            values(CONCAT('Se registro el usuario: ', ?, '. De nombre de usuario: ', ?))", [$firstName, $nickname]);

        return [
            'nickname' => $nickname,
            'email' => $email
        ];
    }

    public function login($loginData)
    {
        $email = $loginData['userEmail'];
        $plainPassword = $loginData['userPassword'];

        // 1. Buscamos al usuario solo por email
        $user = DB::selectOne("SELECT * FROM users WHERE email = ?", [$email]);

        // 2. Si el usuario existe, verificamos la contraseña
        if ($user && password_verify($plainPassword, $user->password)) {
            // Contraseña correcta
            unset($user->password); // Por seguridad, no envíes el hash al frontend

            DB::insert("INSERT into activitylog(userId, activityDescription)
                            values((SELECT userId FROM users WHERE email = ?), 'Se logueo al sistema')", [$email]);
            return $user;
        }

        // 3. Si no existe o la clave no coincide
        return null;
    }

    public function updateUser($userData)
    {
        $userId = $userData['userId'];
        $nickname = $userData['userName'];
        $firstName = $userData['firstName'];
        $lastName = $userData['lastName'];
        $email = $userData['email'];
        $DNI = $userData['DNI'];
        $stateId = $userData['stateId'];
        $address = $userData['address'];
        $municipalitiesId = $userData['municipalitiesId'];
        $zipCode = $userData['zipCode'];
        $phoneNumber = $userData['phoneNumber'];
        $currentImage = $userData['imageUrl']; // Esta puede ser una ruta vieja o un Base64 nuevo

        // --- LÓGICA DE PROCESAMIENTO DE IMAGEN ---
        $finalImagePath = $currentImage;

        // Verificamos si es una imagen nueva en formato Base64
        if (str_contains($currentImage, 'data:image')) {
            // 1. Extraer y decodificar
            $image_service_str = substr($currentImage, strpos($currentImage, ",") + 1);
            $image_data = base64_decode($image_service_str);

            // 2. Generar nombre único
            $fileName = 'user_upd_' . time() . '_' . \Illuminate\Support\Str::random(5) . '.png';
            $finalImagePath = 'users/' . $fileName;

            // 3. Guardar en el disco público (carpeta storage/app/public/users/)
            \Illuminate\Support\Facades\Storage::disk('public')->put($finalImagePath, $image_data);

            // Opcional: Aquí podrías agregar lógica para borrar la foto anterior si existía
        }

        // --- ACTUALIZACIÓN EN LA BASE DE DATOS ---
        DB::update("UPDATE users 
        SET nickname = ?, 
            firstName = ?, 
            lastName = ?, 
            email = ?, 
            DNI = ?, 
            stateId = ?, 
            municipalitiesId = ?, 
            address = ?, 
            zipCode = ?, 
            phoneNumber = ?, 
            ImageUrl = ?
        WHERE userId = ?", [
            $nickname,
            $firstName,
            $lastName,
            $email,
            $DNI,
            $stateId,
            $municipalitiesId,
            $address,
            $zipCode,
            $phoneNumber,
            $finalImagePath, // Guardamos la nueva ruta o mantenemos la vieja
            $userId
        ]);

        // Registro de actividad
        DB::insert("INSERT into activitylog(userId, activityDescription)
                VALUES(?, 'Modifico sus datos personales')", [$userId]);

        return true;
    }

    public function delete($id)
    {

        DB::insert("INSERT into activitylog(userId, activityDescription)
                            values(?, 'Elimino su cuenta')", [$id]);
        DB::update("UPDATE users set isActive = 0 WHERE userId = ?", [$id]);

        return true;
    }

    public function getTiendasSeguidas($userId)
    {
        return DB::select("select s.storeId, s.storeName, c.categoryName, s.storeDescription
from store s
join category c on s.category = c.idCategory
join storefollow sf on s.storeId = sf.idStore
join users u on u.userId = sf.userId
where u.userId = 1 and s.storeIsActive = ?", [$userId]);
    }

    public function getEstados()
    {
        return DB::select("Select * from state");
    }

    public function getMunicipios($idState)
    {
        return DB::select("select m.municipalitiesId, m.municipalityName
            from municipalities m
            join state s on m.stateId = s.stateId
            where s.stateId = ?", [$idState]);
    }

    # TODO: Ver como se hace un insert sin insertar nada
    // ya que en teoria deberia de insertarse los datos
    // automaticamente
    public function visitaSitio()
    {
        return DB::insert("INSERT INTO visitaSitio()");
    }

    public function salidaSitio($idVisitante, $tiempoEnSitio)
    {
        return DB::update("UPDATE visitaSitio set tiempoEnSitio = ? where idVisitante = ?", [$idVisitante, $tiempoEnSitio]);
    }
}
