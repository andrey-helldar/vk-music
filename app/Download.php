<?php

namespace VKMUSIC;

use Illuminate\Database\Eloquent\Model;

class Download extends Model
{
    protected $fillable = [
        'user_id',
        'file_id',
        'audios',
        'expired_at',
    ];
}
