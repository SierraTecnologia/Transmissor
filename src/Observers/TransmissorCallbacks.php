<?php

namespace Transmissor\Observers;

use Event;
use Log;
use Illuminate\Support\Str;
use Support\Models\Base;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;
use Illuminate\Support\Facades\Schema;
use Auth;
use Transmissor\Services\TransmissorService;
use Transmissor\Scopes\TransmissorScope;
/**
 * Call no-op classes on models for all event types.  This just simplifies
 * the handling of model events for models.
 */
class TransmissorCallbacks
{
    /**
     * Handle all model events, both Eloquent and Decoy
     *
     * @param  string $event
     * @param  array  $payload Contains:
     *                         -
     *                         Transmissor\Models\Base
     *                         $model
     * @return void
     */
    public function handle($event, $payload)
    {
        list($model) = $payload;

        // // Payload
        // ^ Cmgmyr\Messenger\Models\Message^ {#4332
        //     #table: "messages"
        //     #touches: array:1 [
        //       0 => "thread"
        //     ]
        //     #fillable: array:3 [
        //       0 => "thread_id"
        //       1 => "user_id"
        //       2 => "body"
        //     ]
        //     #dates: array:1 [
        //       0 => "deleted_at"
        //     ]
        //     #connection: null
        //     #primaryKey: "id"
        //     #keyType: "int"
        //     +incrementing: true
        //     #with: []
        //     #withCount: []
        //     #perPage: 15
        //     +exists: false
        //     +wasRecentlyCreated: false
        //     #attributes: []
        //     #original: []
        //     #changes: []
        //     #casts: []
        //     #dateFormat: null
        //     #appends: []
        //     #dispatchesEvents: []
        //     #observables: []
        //     #relations: []
        //     +timestamps: true
        //     #hidden: []
        //     #visible: []
        //     #guarded: array:1 [
        //       0 => "*"
        //     ]
        //     #forceDeleting: false
        //   }

        // Ignore
        if (!app()->bound(TransmissorService::class)) {
            return true;
        }

        if (!Schema::hasColumn($model->getTable(), 'business_code')) {
            return true;
        }
          

        // Get the action from the event name
        preg_match('#\.(\w+)#', $event, $matches);
        $action = $matches[1];

        // If there is matching callback method on the model, call it, passing
        // any additional event arguments to it
        $method = 'on'.Str::studly($action);

        if ($method == 'onBooting') {
            $model::addGlobalScope(new TransmissorScope);
            return true;
        }

        if ($method == 'onCreating') {
            $business = app()->make(TransmissorService::class);
            $model->business_code = $business->getCode();
            if (Auth::check()) {
                // @todo Verifica se tem acesso
            }
            return true;
        }
        
        if ($method == 'onCreated') {
            
            return true;
        }
        
        if ($method == 'onValidating' || $method == 'onValidated') {

            return true;
        }

        return true;
    }
}
