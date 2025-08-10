<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pizza extends Model
{
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
