<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use Uuids;
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->hasMany(ProductCategory::class, 'product_category_id', 'product_category_id');
    }
}
