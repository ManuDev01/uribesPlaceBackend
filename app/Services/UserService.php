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
            $nickname = $userData['userName'];
            $firstName = $userData['firstName'];
            $lastName = $userData['lastName'];
            $email = $userData['userEmail'];
            $dni = $userData['dni'];

            DB::update("UPDATE users set ? where userId = ?", [$userData, $userId]);
            return true;
        }

        public function delete($id) {
            DB::update("UPDATE users set isActive = 0 WHERE userId = ?", [$id]);

            return true;
        }

    }
