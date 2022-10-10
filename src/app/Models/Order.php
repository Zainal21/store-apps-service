<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    use Uuids;
    
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class, 'user_id', 'id');
    }
}
