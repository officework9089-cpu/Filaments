<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class products extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id', 'name', 'slug', 'sku', 'description', 'image', 'quantity', 'price', 'is_visible', 'is_faetured', 'type', 'publish_At'
    ];

    public function brand() : BelongsTo {
        return $this->belongsTo(Brand::class);
        
    }

    public function categories() : BelongsToMany {
        return $this->belongsToMany(category::class, 'category_product');
    }
}
