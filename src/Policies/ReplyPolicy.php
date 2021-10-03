<?php

namespace Transmissor\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;
use App\Models\Reply;

class ReplyPolicy
{
    use HandlesAuthorization;

    public function delete(User $user, Reply $reply): bool
    {
        return $user->may('manage_topics') || $reply->user_id == $user->id;
    }
}
