<?php

    namespace App\Services;

    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;

    class UserService {

        public function listAll(){
            return DB::select("SELECT * FROM users");
        }

        public function register($userData){
            // Mapeamos los datos del JSON a las columnas reales de tu SQL
            $nickname = $userData['userName']; // Usamos userName del JSON para el nickname
            $firstName = $userData['firstName'] ?? 'Sin Nombre';
            $email = $userData['userEmail'];
            $password = Hash::make($userData['userPassword']);
            $dni = $userData['dni'] ?? '00000000'; // Tu SQL pide DNI como not null

            // INSERT con los nombres de columna exactos de tu uribesPlaceDB.sql
            DB::insert("INSERT INTO users (nickname, firstName, email, password, DNI)
                    VALUES (?, ?, ?, ?, ?)",
                [$nickname, $firstName, $email, $password, $dni]);

                DB::insert("INSERT into activitylog(activityDescription)
                            values(CONCAT('Se registro el usuario: ', ?, '. De nombre de usuario: ', ?))", [$firstName, $nickname]);

            return [
                'nickname' => $nickname,
                'email' => $email
            ];
        }

        public function login($loginData) {
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
            $imageUrl = $userData['imageUrl'];

            DB::update("UPDATE users
            set nickname = ?,
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
            where userId = ?", [$nickname, $firstName, $lastName, $email,
                $DNI, $stateId, $municipalitiesId,$address, $zipCode, $phoneNumber, $imageUrl, $userId]);

                            DB::insert("INSERT into activitylog(userId, activityDescription)
                            values(?, 'Modifico sus datos')", [$userId]);
            return true;
        }

        public function delete($id) {

            DB::insert("INSERT into activitylog(userId, activityDescription)
                            values(?, 'Elimino su cuenta')", [$id]);
            DB::update("UPDATE users set isActive = 0 WHERE userId = ?", [$id]);

            return true;
        }

        public function getTiendasSeguidas($userId){
            return DB::select("select s.storeId, s.storeName, c.categoryName, s.storeDescription
from store s
join category c on s.category = c.idCategory
join storefollow sf on s.storeId = sf.idStore
join users u on u.userId = sf.userId
where u.userId = 1 and s.storeIsActive = ?", [$userId]);
        }

        public function getEstados(){
            return DB::select("Select * from state");
        }

        public function getMunicipios($idState){
            return DB::select("select m.municipalitiesId, m.municipalityName
            from municipalities m
            join state s on m.stateId = s.stateId
            where s.stateId = ?", [$idState]);
        }

    }
