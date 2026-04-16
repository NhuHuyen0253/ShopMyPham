<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model {
    protected $fillable = ['name','supplier_name','position','phone','email','address'];
    public function stockMovements(){ return $this->hasMany(StockMovement::class); }
}

