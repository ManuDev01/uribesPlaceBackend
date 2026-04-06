<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $table = 'store';

    protected $primaryKey = 'storeId';

    protected $fillable = [
        'idOwner',
        'storeName',
        'storeDescription',
        'reputation',
        'category',
        'stateId',
        'municipalitiesId',
        'address',
        'zipCode',
        'phoneNumber',
        'storeIsActive',
        'ImageUrl'
    ];

    const CREATED_AT = 'createAt';
    const UPDATED_AT = 'modifiedAt';

    public function owner()
    {
        return $this->belongsTo(User::class, 'idOwner', 'userId');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'idStore', 'storeId');
    }
}
