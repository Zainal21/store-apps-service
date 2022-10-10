<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiTokenLog extends Model
{
    use HasFactory;

    protected $table = 'token_api_log';

    protected $guarded = [];
}
