<?php

$faktory->define(['thread', 'Transmissor\Models\Messenger\Thread'], function ($f) {
    $f->subject = 'Sample thread';
});

$faktory->define(['message', 'Transmissor\Models\Messenger\Message'], function ($f) {
    $f->user_id = 1;
    $f->thread_id = 1;
    $f->body = 'A message';
});

$faktory->define(['participant', 'Transmissor\Models\Messenger\Participant'], function ($f) {
    $f->user_id = 1;
    $f->thread_id = 1;
});
