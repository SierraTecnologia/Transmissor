<?php

namespace Transmissor\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Jobs\SendNotifyMail;
use App\Models\Message;
use App\Models\Participant;
use App\Models\Thread;
use App\Models\User;
use Muleta\Modules\Features\Markdown\Markdown;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class MessagesController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $threads = Thread::participateBy(Auth::id());
        if (Auth::user()->newThreadsCount() == 0) {
            Auth::user()->message_count = 0;
            Auth::user()->save();
        }
        return view('transmissor::users.messages.index', compact('threads', 'currentUserId'));
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
                                ->delay(config('phphub.notify_delay'));
        dispatch($job);

        // notifications count
        $recipient->message_count++;
        $recipient->save();

        return redirect()->route('profile.transmissor.messages.show', $thread->id);
    }
}
