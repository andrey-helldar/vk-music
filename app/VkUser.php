<?php

namespace VKMUSIC;

use Illuminate\Database\Eloquent\Model;

class VkUser extends Model
{
    protected $fillable = ['user_id', 'user_vk', 'access_token', 'expired_at'];
}
