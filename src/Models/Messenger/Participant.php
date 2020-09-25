<?php

namespace Transmissor\Models\Messenger;

use App\Models\User;
use Cmgmyr\Messenger\Models\Participant as MessengerParticipant;

class Participant extends MessengerParticipant
{
    public function scopeByWhom($query, $user_id)
    {
        /**
          *      'actorable_type' => User::class,
          *      'actorable_id' => Auth::id()
                */
        return $query->where('actorable_type', '=', User::class)->where('actorable_id', '=', $user_id);
    }

    // public static function boot()
    // {
    //     static::updating(function ($model) {
    //         // do some logging
    //         // override some property like $model->something = transform($something);
    //     });
    //     parent::boot();
    // }
}
