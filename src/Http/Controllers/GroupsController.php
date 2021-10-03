<?php

namespace Transmissor\Http\Controllers;

use Transmissor\Conversations\RegisterGroupConversation;
use BotMan\BotMan\BotMan;

class GroupsController extends Controller
{
    public function register(BotMan $bot): void
    {
        $bot->startConversation(new RegisterGroupConversation());
    }

    public function registerNewGroup($payload, BotMan $bot): void
    {
        $this->register($bot);
    }

    /**
     * @return void
     */
    public function registerNewChatMember($payload, BotMan $bot)
    {
        foreach ($payload as $newUser) {
            if ($newUser['is_bot'] && $newUser['id'] === config('botman.telegram.bot.id')) {
                $this->register($bot);

                return;
            }
        }
    }
}
