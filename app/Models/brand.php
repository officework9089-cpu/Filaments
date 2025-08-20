<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class brand extends Model
{
    use HasFactory;

    protected $fillable =[
        'name', 'slug', 'url', 'primary_hex', 'is_visible', 'description'
    ];

    public function brand() : HasMany {
        return $this->hasMany(products::class);
        
    }

}
