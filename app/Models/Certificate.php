<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'document',
        'service',
        'params',
        'status',
        'message',
        'result',
        'region',
        'url_certificate',
    ];

    protected $casts = [
        'result' => 'array'
    ];
}
