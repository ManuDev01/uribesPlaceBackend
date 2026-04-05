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
                return $user;
            }

            // 3. Si no existe o la clave no coincide
            return null;
        }

        public function updateUser($userData)
        {
            $userId = $userData['userId'];

            DB::update("UPDATE users set ? where userId = ?", [$userData, $userId]);
            return true;
        }

        public function delete($id) {
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

    }
