<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
     protected $fillable = [
        'product_name',
        'category_id',
        'product_image',
        'product_desc',
        'status',
        'quantity',
        'price'
    ];
}
