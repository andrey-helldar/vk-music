<?php

namespace VKMUSIC;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Модель пользователя с данными из ВК.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-05
     * @since   1.0
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vk()
    {
        return $this->hasOne(VkUser::class);
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
        return $this->hasMany(Download::class, 'user_id', 'id');
    }
}
