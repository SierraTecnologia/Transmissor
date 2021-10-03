<?php

namespace Transmissor\Http\Controllers\User;

use App\Jobs\SendNotifyMail;
use App\Models\User;
use Muleta\Modules\Features\Markdown\Markdown;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Transmissor\Http\Requests\MessageRequest;
use Transmissor\Http\Controllers\User\Controller;
use Transmissor\Models\Messenger\Message;
use Transmissor\Models\Messenger\Participant;
use Transmissor\Models\Messenger\Thread;

class MessagesController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $currentUserId = Auth::id();
        $threads = Thread::participateBy($currentUserId);

        // @todo Refazer essa parte
        // if (Auth::user()->newThreadsCount() == 0) {
        //     Auth::user()->message_count = 0;
        //     Auth::user()->save();
        // }

        $currentActor = Auth::user();

        dd(
            $currentActor->message_count,
            $currentUserId,
            $threads
        );

        return view(
            'transmissor::users.messages.index',
            compact(
                'threads',
                'currentUserId',
                'currentActor'
            )
        );
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function show($id)
    {
        $thread = Thread::findOrFail($id);
        $participant = $thread->participant();
        $messages = $thread->messages()->recent()->get();

        $this->authorize('show', $thread);

        // counters
        $unread_message_count = $thread->userUnreadMessagesCount(Auth::id());
        if ($unread_message_count > 0) {
            Auth::user()->message_count -= $unread_message_count;
            Auth::user()->save();
        }
        $thread->markAsRead(Auth::id());

        return view('transmissor::users.messages.show', compact('thread', 'participant', 'messages', 'unread_message_count'));
    }

    public function create($id = null)
    {
        if ($id) {
            $recipient = User::findOrFail($id);
            $thread = Thread::between([$recipient->id, Auth::id()])->first();
            if ($thread) {
                return redirect()->route('profile.transmissor.messages.show', $thread->id);
            }
            return view('transmissor::users.messages.create', compact('recipient'));
        }

        $recipient = User::pluck('name');
        return view('transmissor::users.messages.create', compact('recipient'));
    }

    public function createSecureMessage($id)
    {
        // @todo Fazer com as seguintes opcoes
        // timePraAutoDestruicao
        // Grava em disco ou só memoria
        // Criptografia ponta a ponta com limite de data
        $recipient = User::findOrFail($id);

        $thread = Thread::between([$recipient->id, Auth::id()])->first();
        if ($thread) {
            return redirect()->route('profile.transmissor.messages.show', $thread->id);
        }

        return view('transmissor::users.messages.create', compact('recipient'));
    }

    public function store(MessageRequest $request, Markdown $markdown)
    {
        $recipient = User::findOrFail($request->recipient_id);

        if ($request->thread_id) {
            $thread = Thread::findOrFail($request->thread_id);
        } else {
            $subject = Auth::user()->name . ' 给 ' . $recipient->name . ' 的私信。';
            $thread = Thread::create(['subject' => $subject]);
        }

        // Message
        $message = $markdown->convertMarkdownToHtml($request->message);
        Message::create(
            [
                'messageable_type' => Thread::class,
                'messageable_id' => $thread->id,
                'actorable_type' => User::class,
                'actorable_id' => Auth::id(),
                'body' => $message
            ]
        );

        // Sender
        $participant = Participant::firstOrCreate(
            [
                'messageable_type' => Thread::class,
                'messageable_id' => $thread->id,
                'actorable_type' => User::class,
                'actorable_id' => Auth::id()
            ]
        );
        $participant->last_read = new Carbon;
        $participant->save();

        // Recipient
        $thread->addParticipant($recipient->id);

        // Notify user by Email
        $job = (new SendNotifyMail('new_message', Auth::user(), $recipient, null, null, $message))
                                ->delay(\Illuminate\Support\Facades\Config::get('phphub.notify_delay'));
        dispatch($job);

        // notifications count
        $recipient->message_count++;
        $recipient->save();

        return redirect()->route('profile.transmissor.messages.show', $thread->id);
    }
}


/**
 * Exemple
 */
// <?php

// namespace App\Http\Controllers;

// use App\User;
// use Carbon\Carbon;
// use Transmissor\Models\Messenger\Message;
// use Transmissor\Models\Messenger\Participant;
// use Transmissor\Models\Messenger\Thread;
// use Illuminate\Database\Eloquent\ModelNotFoundException;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Request;
// use Illuminate\Support\Facades\Session;

// class MessagesController extends Controller
// {
//     /**
//      * Show all of the message threads to the user.
//      *
//      * @return mixed
//      */
//     public function index()
//     {
//         // All threads, ignore deleted/archived participants
//         $threads = Thread::getAllLatest()->get();

//         // All threads that user is participating in
//         // $threads = Thread::forUser(Auth::id())->latest('updated_at')->get();

//         // All threads that user is participating in, with new messages
//         // $threads = Thread::forUserWithNewMessages(Auth::id())->latest('updated_at')->get();

//         return view('messenger.index', compact('threads'));
//     }

//     /**
//      * Shows a message thread.
//      *
//      * @param $id
//      * @return mixed
//      */
//     public function show($id)
//     {
//         try {
//             $thread = Thread::findOrFail($id);
//         } catch (ModelNotFoundException $e) {
//             Session::flash('error_message', 'The thread with ID: ' . $id . ' was not found.');

//             return redirect()->route('messages');
//         }

//         // show current user in list if not a current participant
//         // $users = User::whereNotIn('id', $thread->participantsUserIds())->get();

//         // don't show the current user in list
//         $userId = Auth::id();
//         $users = User::whereNotIn('id', $thread->participantsUserIds($userId))->get();

//         $thread->markAsRead($userId);

//         return view('messenger.show', compact('thread', 'users'));
//     }

//     /**
//      * Creates a new message thread.
//      *
//      * @return mixed
//      */
//     public function create()
//     {
//         $users = User::where('id', '!=', Auth::id())->get();

//         return view('messenger.create', compact('users'));
//     }

//     /**
//      * Stores a new message thread.
//      *
//      * @return mixed
//      */
//     public function store()
//     {
//         $input = Request::all();

//         $thread = Thread::create([
//             'subject' => $input['subject'],
//         ]);

//         // Message
//         Message::create([
//             'thread_id' => $thread->id,
//             'user_id' => Auth::id(),
//             'body' => $input['message'],
//         ]);

//         // Sender
//         Participant::create([
//             'thread_id' => $thread->id,
//             'user_id' => Auth::id(),
//             'last_read' => new Carbon,
//         ]);

//         // Recipients
//         if (Request::has('recipients')) {
//             $thread->addParticipant($input['recipients']);
//         }

//         return redirect()->route('messages');
//     }

//     /**
//      * Adds a new message to a current thread.
//      *
//      * @param $id
//      * @return mixed
//      */
//     public function update($id)
//     {
//         try {
//             $thread = Thread::findOrFail($id);
//         } catch (ModelNotFoundException $e) {
//             Session::flash('error_message', 'The thread with ID: ' . $id . ' was not found.');

//             return redirect()->route('messages');
//         }

//         $thread->activateAllParticipants();

//         // Message
//         Message::create([
//             'thread_id' => $thread->id,
//             'user_id' => Auth::id(),
//             'body' => Request::input('message'),
//         ]);

//         // Add replier as a participant
//         $participant = Participant::firstOrCreate([
//             'thread_id' => $thread->id,
//             'user_id' => Auth::id(),
//         ]);
//         $participant->last_read = new Carbon;
//         $participant->save();

//         // Recipients
//         if (Request::has('recipients')) {
//             $thread->addParticipant(Request::input('recipients'));
//         }

//         return redirect()->route('messages.show', $id);
//     }
// }
