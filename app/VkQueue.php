<?php

namespace VKMUSIC;

use Illuminate\Database\Eloquent\Model;

class VkQueue extends Model
{
    protected $fillable = [
        'access_token',
        'user_id',
        'method',
        'context',
        'expired_at',
    ];
}
