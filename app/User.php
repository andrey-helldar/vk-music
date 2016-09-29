<?php

namespace VKMUSIC;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        //        'api_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        //        'api_token',
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
}
