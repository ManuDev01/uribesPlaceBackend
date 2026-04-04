<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';
    protected $primaryKey = 'idImage';
    public $timestamps = false;

    protected $fillable = ['idProduct', 'imageUrl', 'isPrimary'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'idProduct');
    }
}
