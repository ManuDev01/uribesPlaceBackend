<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $primaryKey = 'idProduct';

    protected $fillable = [
        'productName',
        'productDescription',
        'brand',
        'price',
        'idStore',
        'idProductQuality',
        'stock',
        'sellCount',
        'SKU'
    ];

    const CREATED_AT = 'createAt';
    const UPDATED_AT = 'modifiedAt';
}
