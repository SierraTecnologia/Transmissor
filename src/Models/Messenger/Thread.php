<?php

namespace Transmissor\Models\Messenger;

use Auth;
use Cmgmyr\Messenger\Models\Thread as MessengerThread;

class Thread extends MessengerThread
{
    public function participant()
    {
        return $this->participants()->where('actorable_type', '=', User::class)->where('actorable_id', '!=', Auth::id())->first()->user;
    }

    public static function participateBy($user_id)
    {
        $user_id = Auth::id();
        $thread_ids = array_unique(Participant::byWhom($user_id)->pluck('messageable_id')->toArray());

        return Thread::whereIn('id', $thread_ids)->orderBy('updated_at', 'desc')->paginate(15);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
