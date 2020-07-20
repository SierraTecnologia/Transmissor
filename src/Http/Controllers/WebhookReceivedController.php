<?php

namespace Transmissor\Http\Controllers;

use Transmissor\Models\User;
use Illuminate\Http\Request;
use Transmissor\Exceptions\WebhookException;

class WebhookReceivedController extends Controller
{

    public function __invoke(Request $request, User $user)
    {
        $signature = $request->header('Transmissor-Signature');

        if (! $signature) {
            throw WebhookException::missingSignature();
        }

        if (! $user->webhook) {
            throw WebhookException::signingSecretNotSet();
        }

        $computedSignature = hash_hmac('sha256', $request->getContent(), decrypt($user->webhook));

        if (! hash_equals($signature, $computedSignature)) {
            throw WebhookException::invalidSignature($signature);
        }

        $eventPayload = json_decode($request->getContent());

        if (! isset($eventPayload->type)) {
            throw WebhookException::missingType();
        }

        $jobClass = $this->determineJobClass($eventPayload->type);

        if (! class_exists($jobClass)) {
            throw WebhookException::unrecognizedType($eventPayload->type);
        }

        dispatch(new $jobClass($eventPayload, $user));

        return response('User will be notified. Thank you Transmissor!');
    }

    protected function determineJobClass(string $type): string
    {
        return '\\Transmissor\\Jobs\\Webhook\\'.ucfirst($type);
    }
}
