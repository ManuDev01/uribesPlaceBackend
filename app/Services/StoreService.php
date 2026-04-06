<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoreService {

    public function listAllActive() {
        return DB::select("SELECT * FROM store WHERE storeIsActive = 1");
    }

    public function create($data) {
        $imageUrl = null;

        if (isset($data['imageUrl'])) {
            $image_service_str = substr($data['imageUrl'], strpos($data['imageUrl'], ",") + 1);
            $image_data = base64_decode($image_service_str);
            $fileName = 'store_' . time() . '_' . Str::random(5) . '.png';
            $imageUrl = 'stores/' . $fileName;
            Storage::disk('public')->put($imageUrl, $image_data);
        }

        DB::insert("INSERT INTO store
            (idOwner, storeName, storeDescription, reputation, category, stateId, municipalitiesId, address, zipCode, phoneNumber, storeIsActive, ImageUrl)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)",
            [
                $data['idOwner'], $data['storeName'], $data['storeDescription'],
                $data['reputation'] ?? 0, $data['category'], $data['stateId'],
                $data['municipalitiesId'], $data['address'], $data['zipCode'],
                $data['phoneNumber'], $imageUrl
            ]);

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

        return DB::update("UPDATE store SET $fields WHERE storeId = ?", $values);
    }

    public function deactivate($id) {
        return DB::update("UPDATE store SET storeIsActive = 0 WHERE storeId = ?", [$id]);
    }
}
