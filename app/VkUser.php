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
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * Получить список файлов пользователя.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-10-07
     * @since   1.0
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(Download::class, 'user_id', 'user_id');
    }
}
