<?php

namespace Transmissor\Http\Controllers\User;

use BotMan\BotMan\BotMan;
use Transmissor\Conversations\StartConversation;
use Transmissor\Http\Controllers\Controller;

class StoreController extends Controller
{

    /**
     * Handle the incoming request.
     *
     * @param \BotMan\BotMan\BotMan $bot
     *
     * @return void
     */
    public function __invoke(BotMan $bot)
    {
        $bot->startConversation(new StartConversation());
    }
}
