<?php

namespace Transmissor\Tests\Stubs\Models;

use Transmissor\Models\Messenger\Participant;

class CustomParticipant extends Participant
{
    protected $table = 'custom_participants';
}
