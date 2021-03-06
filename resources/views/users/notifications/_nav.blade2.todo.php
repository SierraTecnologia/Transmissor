<div class="box panel car">
    <div class="padding-md">
        <div class="list-group text-center">
          <a href="{{ route('profile.transmissor.messages.index') }}" class="list-group-item big {{ active_class(if_uri_pattern(['messages*'])) }}">
              <i class="text-md fa fa-envelope" aria-hidden="true"></i>
              &nbsp;Comunicação pessoal
               @if ($currentActor->message_count > 0)
                   <span class="badge badge-important" style="color: white;">
                      {{ $currentActor->message_count }}
                  </span>
               @endif
          </a>

          <a href="{{ route('profile.notifications.index') }}" class="list-group-item big {{ active_class(if_route('notifications.index')) }}">
              <i class="text-md fa fa-bell" aria-hidden="true"></i>
               &nbsp;Aviso
               @if ($currentActor->notification_count > 0)
                   <span class="badge badge-important" style="color: white;">
                      {{ $currentActor->notification_count }}
                  </span>
               @endif
           </a>
        </div>
    </div>
</div>
