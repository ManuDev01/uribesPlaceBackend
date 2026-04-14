<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreService {

    public function listAllActive() {
        return DB::select("SELECT * FROM store WHERE storeIsActive = 1");
    }

    public function create($data, $userId) {
        $existingStore = DB::select("SELECT storeId FROM store WHERE idOwner = ? AND storeIsActive = 1", [$userId]);

        if (!empty($existingStore)) {
            throw new \Exception("El usuario ya posee una tienda activa.", 409);
        }

        $imageUrl = null;
        if (isset($data['imageUrl']) && !empty($data['imageUrl'])) {
            $image_service_str = substr($data['imageUrl'], strpos($data['imageUrl'], ",") + 1);
            $image_data = base64_decode($image_service_str);
            $fileName = 'store_' . time() . '_' . \Illuminate\Support\Str::random(5) . '.png';
            $imageUrl = 'stores/' . $fileName;
            \Illuminate\Support\Facades\Storage::disk('public')->put($imageUrl, $image_data);
        }

        DB::insert("INSERT INTO store
            (idOwner, storeName, storeDescription, category, stateId, municipalitiesId, address, zipCode, phoneNumber, storeIsActive, ImageUrl)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)",
            [
                $userId,
                $data['storeName'],
                $data['storeDescription'],
                $data['category'],
                $data['stateId'],
                $data['municipalitiesId'],
                $data['address'],
                $data['zipCode'],
                $data['phoneNumber'],
                $imageUrl
            ]);

            DB::insert("INSERT into activitylog(activityDescription)
                            values(CONCAT('Se registro la tienda: ', ?, '. De nombre de tienda: ', ?))", [$data['storeName'], $data['storeName']]);

        return [
            'storeName' => $data['storeName'],
        ];

        return DB::getPdo()->lastInsertId();
    }

    public function findByOwner($idOwner) {
        return DB::select("SELECT * FROM store WHERE idOwner = ? AND storeIsActive = 1", [$idOwner]);
    }

    public function update($id, $data) {
        $fields = "";
        $values = [];
        foreach ($data as $key => $value) {
            if ($key !== 'storeId' && $key !== 'idOwner' && $key !== 'imageUrl') {
                $fields .= "$key = ?, ";
                $values[] = $value;
            }
        }
        $fields .= "modifiedAt = NOW()";
        $values[] = $id;

        DB::insert("INSERT into activitylog(activityDescription)
                            values(CONCAT('Se actualizo la tienda: ', ?, '. De nombre de tienda: ', ?))", [$data['storeName'], $data['storeName']]);

        return [
            'storeName' => $data['storeName'],
            'email' => $data['email']
        ];

        return DB::update("UPDATE store SET $fields WHERE storeId = ?", $values);
    }

    public function rateStore($storeId, $userId, $ratingValue) {
        DB::statement("
            INSERT INTO store_ratings (idStore, idUser, rating)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE rating = VALUES(rating), modifiedAt = NOW()
        ", [$storeId, $userId, $ratingValue]);

        $averageResult = DB::select("
            SELECT AVG(rating) as average
            FROM store_ratings
            WHERE idStore = ?
        ", [$storeId]);

        $newAverage = $averageResult[0]->average ?? 0;

        return DB::update("
            UPDATE store
            SET reputation = ?
            WHERE storeId = ?
        ", [$newAverage, $storeId]);
    }

    public function registerVisit($storeId, $userId = null, $ip = null) {
        return DB::insert("INSERT INTO store_visits (idStore, idUser, ipAddress) VALUES (?, ?, ?)",
                          [$storeId, $userId, $ip]);
    }

    public function getMostVisited($limit = 5) {
        return DB::select("
            SELECT s.storeName, COUNT(v.idVisit) as total_visits
            FROM store s
            LEFT JOIN store_visits v ON s.storeId = v.idStore
            WHERE s.storeIsActive = 1
            GROUP BY s.storeId, s.storeName
            ORDER BY total_visits DESC
            LIMIT ?
        ", [$limit]);
    }

    public function deactivate($id, $data) {

        DB::insert("INSERT into activitylog(activityDescription)
                            values(CONCAT('Se elimino la tienda: ', ?, '. De nombre de tienda: ', ?))", [$data['storeName'], $data['storeName']]);

        return [
            'storeName' => $data['storeName'],
            'email' => $data['email']
        ];

        return DB::update("UPDATE store SET storeIsActive = 0 WHERE storeId = ?", [$id]);
    }
}
