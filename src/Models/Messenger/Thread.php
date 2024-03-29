<?php

namespace Transmissor\Models\Messenger;

use Auth;
use Cmgmyr\Messenger\Models\Thread as MessengerThread;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Transmissor\Models\Messenger\Participant;
use Transmissor\Models\Messenger\Message;
use Transmissor\Models\Messenger\Models;

class Thread extends MessengerThread
{

    /**
     * Get all participants.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function participants(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Participant::class, 'messageable');
    }
    /**
     * Get all messages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function messages(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Message::class, 'messageable');
    }

    // Eu tinha feito essas duas mas apaguei
    // public function participant()
    // {
    //     return $this->participants()->where('actorable_type', '=', User::class)->where('actorable_id', '!=', Auth::id())->first()->user;
    // }

    // public static function participateBy($user_id)
    // {
    //     $user_id = Auth::id();
    //     $thread_ids = array_unique(Participant::byWhom($user_id)->pluck('messageable_id')->toArray());

    //     return Thread::whereIn('id', $thread_ids)->orderBy('updated_at', 'desc')->paginate(15);
    // }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // /**
    //  * The database table used by the model.
    //  *
    //  * @var string
    //  */
    // protected $table = 'threads';

    // /**
    //  * The attributes that can be set with Mass Assignment.
    //  *
    //  * @var array
    //  */
    // protected $fillable = ['subject'];

    // /**
    //  * The attributes that should be mutated to dates.
    //  *
    //  * @var array
    //  */
    // protected $dates = ['deleted_at'];

    // /**
    //  * Internal cache for creator.
    //  *
    //  * @var null|Models::user()|\Illuminate\Database\Eloquent\Model
    //  */
    // protected $creatorCache;
    
    // /**
    //  * {@inheritDoc}
    //  */
    // public function __construct(array $attributes = [])
    // {
    //     $this->table = Models::table('threads');

    //     parent::__construct($attributes);
    // }

    // /**
    //  * Messages relationship.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\HasMany
    //  *
    //  * @codeCoverageIgnore
    //  */
    // public function messages()
    // {
    //     return $this->hasMany(Models::classname(Message::class), 'thread_id', 'id');
    // }

    // /**
    //  * Returns the latest message from a thread.
    //  *
    //  * @return null|\Transmissor\Models\Messenger\Message
    //  */
    // public function getLatestMessageAttribute()
    // {
    //     return $this->messages()->latest()->first();
    // }

    // /**
    //  * Participants relationship.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\HasMany
    //  *
    //  * @codeCoverageIgnore
    //  */
    // public function participants()
    // {
    //     return $this->hasMany(Models::classname(Participant::class), 'thread_id', 'id');
    // }

    // /**
    //  * User's relationship.
    //  *
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
    //  *
    //  * @codeCoverageIgnore
    //  */
    // public function users()
    // {
    //     return $this->belongsToMany(Models::classname('User'), Models::table('participants'), 'thread_id', 'user_id');
    // }

    // /**
    //  * Returns the user object that created the thread.
    //  *
    //  * @return null|Models::user()|\Illuminate\Database\Eloquent\Model
    //  */
    // public function creator()
    // {
    //     if ($this->creatorCache === null) {
    //         $firstMessage = $this->messages()->withTrashed()->oldest()->first();
    //         $this->creatorCache = $firstMessage ? $firstMessage->user : Models::user();
    //     }

    //     return $this->creatorCache;
    // }

    // /**
    //  * Returns all of the latest threads by updated_at date.
    //  *
    //  * @return \Illuminate\Database\Query\Builder|static
    //  */
    // public static function getAllLatest()
    // {
    //     return static::latest('updated_at');
    // }

    // /**
    //  * Returns all threads by subject.
    //  *
    //  * @param string $subject
    //  *
    //  * @return \Illuminate\Database\Eloquent\Collection|static[]
    //  */
    // public static function getBySubject($subject)
    // {
    //     return static::where('subject', 'like', $subject)->get();
    // }

    // /**
    //  * Returns an array of user ids that are associated with the thread.
    //  *
    //  * @param null|int $userId
    //  *
    //  * @return array
    //  */
    // public function participantsUserIds($userId = null)
    // {
    //     $users = $this->participants()->withTrashed()->select('user_id')->get()->map(function ($participant) {
    //         return $participant->user_id;
    //     });

    //     if ($userId !== null) {
    //         $users->push($userId);
    //     }

    //     return $users->toArray();
    // }

    // /**
    //  * Returns threads that the user is associated with.
    //  *
    //  * @param \Illuminate\Database\Eloquent\Builder $query
    //  * @param int $userId
    //  *
    //  * @return \Illuminate\Database\Eloquent\Builder
    //  */
    // public function scopeForUser(Builder $query, $userId)
    // {
    //     $participantsTable = Models::table('participants');
    //     $threadsTable = Models::table('threads');

    //     return $query->join($participantsTable, $this->getQualifiedKeyName(), '=', $participantsTable . '.thread_id')
    //         ->where($participantsTable . '.user_id', $userId)
    //         ->whereNull($participantsTable . '.deleted_at')
    //         ->select($threadsTable . '.*');
    // }

    // /**
    //  * Returns threads with new messages that the user is associated with.
    //  *
    //  * @param \Illuminate\Database\Eloquent\Builder $query
    //  * @param int $userId
    //  *
    //  * @return \Illuminate\Database\Eloquent\Builder
    //  */
    // public function scopeForUserWithNewMessages(Builder $query, $userId)
    // {
    //     $participantTable = Models::table('participants');
    //     $threadsTable = Models::table('threads');

    //     return $query->join($participantTable, $this->getQualifiedKeyName(), '=', $participantTable . '.thread_id')
    //         ->where($participantTable . '.user_id', $userId)
    //         ->whereNull($participantTable . '.deleted_at')
    //         ->where(function (Builder $query) use ($participantTable, $threadsTable) {
    //             $query->where($threadsTable . '.updated_at', '>', $this->getConnection()->raw($this->getConnection()->getTablePrefix() . $participantTable . '.last_read'))
    //                 ->orWhereNull($participantTable . '.last_read');
    //         })
    //         ->select($threadsTable . '.*');
    // }

    // /**
    //  * Returns threads between given user ids.
    //  *
    //  * @param \Illuminate\Database\Eloquent\Builder $query
    //  * @param array $participants
    //  *
    //  * @return \Illuminate\Database\Eloquent\Builder
    //  */
    // public function scopeBetween(Builder $query, array $participants)
    // {
    //     return $query->whereHas('participants', function (Builder $q) use ($participants) {
    //         $q->whereIn('user_id', $participants)
    //             ->select($this->getConnection()->raw('DISTINCT(thread_id)'))
    //             ->groupBy('thread_id')
    //             ->havingRaw('COUNT(thread_id)=' . count($participants));
    //     });
    // }

    // /**
    //  * Add users to thread as participants.
    //  *
    //  * @param array|mixed $userId
    //  *
    //  * @return void
    //  */
    // public function addParticipant($userId)
    // {
    //     $userIds = is_array($userId) ? $userId : (array) func_get_args();

    //     collect($userIds)->each(function ($userId) {
    //         Models::participant()->firstOrCreate([
    //             'user_id' => $userId,
    //             'thread_id' => $this->id,
    //         ]);
    //     });
    // }

    // /**
    //  * Remove participants from thread.
    //  *
    //  * @param array|mixed $userId
    //  *
    //  * @return void
    //  */
    // public function removeParticipant($userId)
    // {
    //     $userIds = is_array($userId) ? $userId : (array) func_get_args();

    //     Models::participant()->where('thread_id', $this->id)->whereIn('user_id', $userIds)->delete();
    // }

    // /**
    //  * Mark a thread as read for a user.
    //  *
    //  * @param int $userId
    //  *
    //  * @return void
    //  */
    // public function markAsRead($userId)
    // {
    //     try {
    //         $participant = $this->getParticipantFromUser($userId);
    //         $participant->last_read = new Carbon();
    //         $participant->save();
    //     } catch (ModelNotFoundException $e) { // @codeCoverageIgnore
    //         // do nothing
    //     }
    // }

    // /**
    //  * See if the current thread is unread by the user.
    //  *
    //  * @param int $userId
    //  *
    //  * @return bool
    //  */
    // public function isUnread($userId)
    // {
    //     try {
    //         $participant = $this->getParticipantFromUser($userId);

    //         if ($participant->last_read === null || $this->updated_at->gt($participant->last_read)) {
    //             return true;
    //         }
    //     } catch (ModelNotFoundException $e) { // @codeCoverageIgnore
    //         // do nothing
    //     }

    //     return false;
    // }

    // /**
    //  * Finds the participant record from a user id.
    //  *
    //  * @param $userId
    //  *
    //  * @return mixed
    //  *
    //  * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
    //  */
    // public function getParticipantFromUser($userId)
    // {
    //     return $this->participants()->where('user_id', $userId)->firstOrFail();
    // }

    // /**
    //  * Restores only trashed participants within a thread that has a new message.
    //  * Others are already active participiants.
    //  *
    //  * @return void
    //  */
    // public function activateAllParticipants()
    // {
    //     $participants = $this->participants()->onlyTrashed()->get();
    //     foreach ($participants as $participant) {
    //         $participant->restore();
    //     }
    // }

    // /**
    //  * Generates a string of participant information.
    //  *
    //  * @param null|int $userId
    //  * @param array $columns
    //  *
    //  * @return string
    //  */
    // public function participantsString($userId = null, $columns = ['name'])
    // {
    //     $participantsTable = Models::table('participants');
    //     $usersTable = Models::table('users');
    //     $userPrimaryKey = Models::user()->getKeyName();

    //     $selectString = $this->createSelectString($columns);

    //     $participantNames = $this->getConnection()->table($usersTable)
    //         ->join($participantsTable, $usersTable . '.' . $userPrimaryKey, '=', $participantsTable . '.user_id')
    //         ->where($participantsTable . '.thread_id', $this->id)
    //         ->select($this->getConnection()->raw($selectString));

    //     if ($userId !== null) {
    //         $participantNames->where($usersTable . '.' . $userPrimaryKey, '!=', $userId);
    //     }

    //     return $participantNames->implode('name', ', ');
    // }

    // /**
    //  * Checks to see if a user is a current participant of the thread.
    //  *
    //  * @param int $userId
    //  *
    //  * @return bool
    //  */
    // public function hasParticipant($userId)
    // {
    //     $participants = $this->participants()->where('user_id', '=', $userId);

    //     return $participants->count() > 0;
    // }

    // /**
    //  * Generates a select string used in participantsString().
    //  *
    //  * @param array $columns
    //  *
    //  * @return string
    //  */
    // protected function createSelectString($columns)
    // {
    //     $dbDriver = $this->getConnection()->getDriverName();
    //     $tablePrefix = $this->getConnection()->getTablePrefix();
    //     $usersTable = Models::table('users');

    //     switch ($dbDriver) {
    //     case 'pgsql':
    //     case 'sqlite':
    //         $columnString = implode(" || ' ' || " . $tablePrefix . $usersTable . '.', $columns);
    //         $selectString = '(' . $tablePrefix . $usersTable . '.' . $columnString . ') as name';
    //         break;
    //     case 'sqlsrv':
    //         $columnString = implode(" + ' ' + " . $tablePrefix . $usersTable . '.', $columns);
    //         $selectString = '(' . $tablePrefix . $usersTable . '.' . $columnString . ') as name';
    //         break;
    //     default:
    //         $columnString = implode(", ' ', " . $tablePrefix . $usersTable . '.', $columns);
    //         $selectString = 'concat(' . $tablePrefix . $usersTable . '.' . $columnString . ') as name';
    //     }

    //     return $selectString;
    // }

    // /**
    //  * Returns array of unread messages in thread for given user.
    //  *
    //  * @param int $userId
    //  *
    //  * @return \Illuminate\Support\Collection
    //  */
    // public function userUnreadMessages($userId)
    // {
    //     $messages = $this->messages()->where('user_id', '!=', $userId)->get();

    //     try {
    //         $participant = $this->getParticipantFromUser($userId);
    //     } catch (ModelNotFoundException $e) {
    //         return collect();
    //     }

    //     if (!$participant->last_read) {
    //         return $messages;
    //     }

    //     return $messages->filter(function ($message) use ($participant) {
    //         return $message->updated_at->gt($participant->last_read);
    //     });
    // }

    // /**
    //  * Returns count of unread messages in thread for given user.
    //  *
    //  * @param int $userId
    //  *
    //  * @return int
    //  */
    // public function userUnreadMessagesCount($userId)
    // {
    //     return $this->userUnreadMessages($userId)->count();
    // }
}
