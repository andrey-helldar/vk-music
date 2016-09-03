<?php

namespace VKMUSIC;

use Illuminate\Database\Eloquent\Model;

class VkResponse extends Model
{
    protected $fillable = [
        'access_token',
        'user_id',
        'method',
        'context',
    ];
}
