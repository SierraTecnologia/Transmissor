<?php

namespace Transmissor\Tests\Stubs\Models;

use Transmissor\Models\Messenger\Thread;

class CustomThread extends Thread
{
    protected $table = 'custom_threads';
}
