@extends('layouts.app')

@section('title')
Nova mensagem privada | @parent
@stop

@section('content')
<?php
if (! $recipient instanceof \Illuminate\Database\Eloquent\Model) {
    // if (!is_object($recipient)) {
    $users = $recipient;
    $recipient = false;
}
?>
<div class="messages row">

    <div class="col-md-3 main-col">
        @include('transmissor::users.messages._nav')
    </div>

    <div class="col-md-9  left-col ">

        <div class="panel panel-default padding-sm">

            <div class="panel-heading ">
                <h1>
                    Envie uma mensagem
                </h1>
            </div>

            <div class="panel-body">
                @if ($recipient)
                    <div>
                        <a href="{{ route('components.actors.profile.show', [$recipient->id]) }}" title="{{ $recipient->name }}">
                            <img class="avatar avatar-small" alt="{{ $recipient->name }}" src="{{ $recipient->present()->gravatar }}"/>
                            {{ $recipient->name }}
                        </a>
                    </div>
                    <br>
                @endif

                <form class="form-horizontal" method="POST" action="{{ route('profile.transmissor.messages.store') }}" accept-charset="UTF-8">
                    {!! csrf_field() !!}

                        @include('pedreiro::partials.errors')

                        <div class="form-group">
                            @if ($recipient)
                                <input name="recipient_id" type="hidden" value="{{ $recipient->id }}">
                            @else 
                                <?= Former::select('recipient_id')->options($users) ?>
                            @endif
                              <div class="col-sm-8">
                                  <textarea class="form-control" rows="5" name="message" cols="50" id="reply_content" required></textarea>
                              </div>
                              <div class="col-sm-4 help-block">
                                    <ul>
                                        <li>Não ofenda ninguem</li>
                                        <li>Pode ser usado marcação de texto (Linguagem Markdown)</li>
                                        <li>Suporte a upload de imagem</li>
                                        <li>Suporte a Emojis</li>
                                    </ul>
                              </div>
                        </div>

                        <button type="submit" class="btn btn-primary loading-on-clicked"><i class="fa fa-paper-plane" aria-hidden="true"></i> Enviar</button>

                </form>
            </div>
        </div>
    </div>
</div>


@stop


