<?php

namespace App\Models;

use App\Models\Unit;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Purchase extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];


    public function product(){
        return $this->belongsTo(Product::class);
    }
 
     public function supplier(){
        return $this->belongsTo(Supplier::class);
    }
 
     public function unit(){
        return $this->belongsTo(Unit::class);
    }

     public function category(){
        return $this->belongsTo(Category::class);
     }
}
