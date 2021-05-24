<?php

namespace Transmissor\Tests\Stubs\Models;

use Transmissor\Models\Messenger\Message;

class CustomMessage extends Message
{
    protected $table = 'custom_messages';
}
