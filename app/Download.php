<?php

namespace VKMUSIC;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = [
        'user_id',
        'file_id',
        'expired_at',
    ];
}
