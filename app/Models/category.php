<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class category extends Model
{
    use HasFactory;

    protected $fillable = [

        'name', 'slug', 'parent_id', 'is_visible', 'description'
    ];

    public function parent() : BelongsTo {
        return $this->belongsTo(category::class, 'parent_id');
        
    }
    public function child() : HasMany {
     return $this->hasMany(category::class, 'parent_id');
    }

    public function products()
    {
        return $this->belongsToMany(Products::class, 'category_product', 'category_id', 'product_id');
    }

}
 