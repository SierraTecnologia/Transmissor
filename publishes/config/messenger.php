<?php

return [

    'user_model' => \Illuminate\Support\Facades\Config::get('sitec.core.models.user', \App\Models\User::class),

    'message_model' => Transmissor\Models\Messenger\Message::class,

    'participant_model' => Transmissor\Models\Messenger\Participant::class,

    'thread_model' => Transmissor\Models\Messenger\Thread::class,

    /**
     * Define custom database table names - without prefixes.
     */
    'messages_table' => null,

    'participants_table' => null,

    'threads_table' => null,
];
