<?php

namespace Transmissor\Http\Middleware;

use Transmissor\Models\User;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Received;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
use Illuminate\Support\Facades\Hash;
use Transmissor\Services\UserService;

class LoadUserMiddleware implements Received
{


    /**
     * Handle an incoming message.
     *
     * @param IncomingMessage $message
     * @param callable $next
     * @param BotMan $bot
     *
     * @return mixed
     */
    public function received(IncomingMessage $message, $next, BotMan $bot)
    {
        // $botUser = $bot->getDriver()->getUser($message);

        // $user = User::firstOrCreate(['telegram_id' => $botUser->getId()], [
        //     'name' => $botUser->getFirstName() ?? $botUser->getId(),
        //     'surname' => $botUser->getLastName(),
        //     'username' => $botUser->getUsername(),
        //     'email' => $botUser->getId() . '@Transmissorbot.com',
        //     'password' => Hash::make($botUser->getUsername() . '-Transmissorbot'),
        // ]);

        // auth()->login($user);

        // return $next($message);
        $user = (new UserService())->findOrCreate($bot->getDriver()->getUser($message));

        auth()->login($user);

        $this->setUserLanguage($message);

        $this->checkIsGroupConversation($message, $bot);

        $this->checkIsNotTalkingToBot($message, $bot);

        $this->loadUserInGroup($message, $bot);

        return $next($message);
    }

    private function setUserLanguage(IncomingMessage $message)
    {
        if (isset($message->getPayload()['from']['language_code'])) {
            app()->setLocale($message->getPayload()['from']['language_code']);
        }
    }

    /**
     * @param \BotMan\BotMan\Messages\Incoming\IncomingMessage $message
     * @param \BotMan\BotMan\BotMan $bot
     *
     * @throws \Transmissor\Exceptions\PrivateConversationNotAllowedException
     * @throws \BotMan\BotMan\Exceptions\Base\BotManException
     */
    private function checkIsGroupConversation(IncomingMessage $message, BotMan $bot)
    {
        if ($message->getPayload()['chat']['type'] === 'group') {
            return;
        }

        $bot->say(trans('errors.bot_is_for_groups'), $message->getRecipient());

        throw new PrivateConversationNotAllowedException();
    }

    /**
     * @param \BotMan\BotMan\Messages\Incoming\IncomingMessage $message
     * @param \BotMan\BotMan\BotMan $bot
     *
     * @throws \Transmissor\Exceptions\InteractingWithBotException
     * @throws \BotMan\BotMan\Exceptions\Base\BotManException
     */
    private function checkIsNotTalkingToBot(IncomingMessage $message, BotMan $bot)
    {
        if (strpos($message->getText(), '@'.config('botman.telegram.bot.username')) !== false) {

            $bot->say(trans('debts.you_cannot_debt_to_bot'), $message->getRecipient());

            throw new InteractingWithBotException();
        }
    }

    /**
     * @param \BotMan\BotMan\Messages\Incoming\IncomingMessage $message
     * @param \BotMan\BotMan\BotMan $bot
     *
     * @throws \Transmissor\Exceptions\MissingGroupException
     * @throws \BotMan\BotMan\Exceptions\Base\BotManException
     */
    private function loadUserInGroup(IncomingMessage $message, BotMan $bot)
    {
        $user = User::findOrCreateTelegram($bot->getDriver()->getUser($message));

        auth()->login($user);

        $group = Group::where('telegram_id', collect($message->getPayload())->get('chat')['id'])->first();

        if ($group) {
            $user->addToGroup($group);

            $user->group = $group;

            app()->setLocale($group->language);

        } elseif (! $this->isRegisteringGroup($message, $bot)) {
            $bot->say(trans('groups.first_register'), $message->getRecipient());

            throw new MissingGroupException();
        }
    }

    private function isRegisteringGroup(IncomingMessage $message, BotMan $bot)
    {
        $payload = collect($message->getPayload());

        $conversation = $bot->getStoredConversation($message);

        return $payload->contains('new_chat_members')
            || $payload->contains('group_chat_created')
            || $message->getText() === '/register'
            || ($conversation && $conversation['conversation'] instanceof RegisterGroupConversation);
    }
}
