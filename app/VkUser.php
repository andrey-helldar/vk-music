<?php

namespace VKMUSIC;

use Illuminate\Database\Eloquent\Model;

class VkUser extends Model
{
    protected $fillable = ['user_id', 'user_vk', 'access_token', 'expired_at'];

    /**
     * Получаем модель юзера.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-15
     * @since   1.0
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    protected function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
