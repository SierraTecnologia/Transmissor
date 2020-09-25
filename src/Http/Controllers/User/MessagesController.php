<?php

namespace Transmissor\Http\Controllers\User;

use App\Models\User;
use Carbon\Carbon;
use Transmissor\Models\Messenger\Message;
use Transmissor\Models\Messenger\Participant;
use Transmissor\Models\Messenger\Thread;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Phphub\Markdown\Markdown;
use App\Jobs\SendNotifyMail;
use Support\Http\Requests\MessageRequest;
use Transmissor\Http\Controllers\User\Controller;

class MessagesController extends Controller
{
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


        return view(
            'facilitador::users.messages.index',
            compact(
                'threads',
                'currentUserId',
                'currentActor'
            )
        );
    }

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

        return view('facilitador::users.messages.show', compact('thread', 'participant', 'messages', 'unread_message_count'));
    }

    public function create($id)
    {
        $recipient = User::findOrFail($id);

        $thread = Thread::between([$recipient->id, Auth::id()])->first();
        if ($thread) {
            return redirect()->route('messages.show', $thread->id);
        }

        return view('facilitador::users.messages.create', compact('recipient'));
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
            return redirect()->route('messages.show', $thread->id);
        }

        return view('facilitador::users.messages.create', compact('recipient'));
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
        Message::create(['thread_id' => $thread->id, 'user_id' => Auth::id(), 'body' => $message]);

        // Sender
        $participant = Participant::firstOrCreate(['thread_id' => $thread->id, 'user_id' => Auth::id()]);
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

        return redirect()->route('messages.show', $thread->id);
    }
}