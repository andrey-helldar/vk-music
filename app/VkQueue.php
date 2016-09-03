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

    /**
     * Модель юзера.
     *
     * @author  Andrey Helldar <helldar@ai-rus.com>
     * @version 2016-09-03
     * @since   1.0
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user()
    {
        return $this->hasOne(VkUser::class, 'user_id', 'user_id');
    }
}
