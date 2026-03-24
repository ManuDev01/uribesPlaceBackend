<?php

    namespace App\Services;

    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Hash;

    class UserService {

        public function listAll(){
            return DB::select("SELECT * FROM users");
        }

        public function register($userData) {
            // Extraemos los datos usando las llaves correctas que enviaremos desde el JSON
            $name     = $userData['userName'];
            $email    = $userData['userEmail'];
            $password = Hash::make($userData['userPassword']);
            $phone    = $userData['userPhone'] ?? null;
            $address  = $userData['userAddress'] ?? null;

            // La consulta SQL con los nombres de columnas exactos de tu imagen
            DB::insert("INSERT INTO users (userName, userEmail, userPassword, userPhone, userAddress) 
                        VALUES (?, ?, ?, ?, ?)", 
                        [$name, $email, $password, $phone, $address]);

            return [
                'userName' => $name, 
                'userEmail' => $email
            ];
        }

    }
