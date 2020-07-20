<?php

namespace Transmissor\Models;

// use Cmgmyr\Messenger\Models\Message as BaseMessage;
use Transmissor\Models\Messenger\Message as BaseMessage;

class Message extends BaseMessage
{
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
