<?php

namespace Transmissor\Models\Messenger;

use App\Models\User;
use Cmgmyr\Messenger\Models\Participant as MessengerParticipant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Transmissor\Models\Messenger\Message;
use Transmissor\Models\Messenger\Models;
use Transmissor\Models\Messenger\Thread;

class Participant extends MessengerParticipant
{
    public function scopeByWhom($query, $user_id)
    {
        /**
          *      'actorable_type' => User::class,
          *      'actorable_id' => Auth::id()
                */
        return $query->where('actorable_type', '=', User::class)->where('actorable_id', '=', $user_id);
    }

    // public static function boot()
    // {
    //     static::updating(function ($model) {
    //         // do some logging
    //         // override some property like $model->something = transform($something);
    //     });
    //     parent::boot();
    // }

    
    /**
     * Local aonde está a mensagem
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function messageable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }
    
    /**
     * Quem enviou a mensagem
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function actorable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    // use SoftDeletes;

    // /**
    //  * The database table used by the model.
    //  *
    //  * @var string
    //  */
    // protected $table = 'participants';

    // /**
    //  * The attributes that can be set with Mass Assignment.
    //  *
    //  * @var array
    //  */
    // protected $fillable = ['thread_id', 'user_id', 'last_read'];

    // /**
    //  * The attributes that should be mutated to dates.
    //  *
    //  * @var array
    //  */
    // protected $dates = ['deleted_at', 'last_read'];

    // /**
    //  * {@inheritDoc}
    //  */
    // public function __construct(array $attributes = [])
    // {
    //     $this->table = Models::table('participants');

    //     parent::__construct($attributes);
    // }

    // /**
    //  * Thread relationship.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    //  *
    //  * @codeCoverageIgnore
    //  */
    // public function thread()
    // {
    //     return $this->belongsTo(Models::classname(Thread::class), 'thread_id', 'id');
    // }

    // /**
    //  * User relationship.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    //  *
    //  * @codeCoverageIgnore
    //  */
    // public function user()
    // {
    //     return $this->belongsTo(Models::user(), 'user_id');
    // }
}
