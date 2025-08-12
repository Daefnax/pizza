<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Drink extends Model
{
    protected $fillable = ['product_id'];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
