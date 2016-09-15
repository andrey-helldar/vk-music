<?php

namespace VKMUSIC;

use Illuminate\Database\Eloquent\Model;

class VkError extends Model
{
    protected $fillable = [
        'user_id',
        'method',
        'context',
    ];
}
