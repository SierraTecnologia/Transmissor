<?php

namespace Transmissor\Http\Controllers\Users;

use Transmissor\Conversations\StartConversation;
use Transmissor\Http\Controllers\Controller;
use BotMan\BotMan\BotMan;

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
