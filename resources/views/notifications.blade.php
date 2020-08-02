@if (count($errors->all()) > 0)
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4>Error</h4>
	Please check the form below for errors
</div>
@endif @if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4>Success</h4>
	@if(is_array($message)) @foreach ($message as $m) {{ $m }} @endforeach
	@else {{ $message }} @endif
</div>
@endif @if ($message = Session::get('error'))
<div class="alert alert-danger alert-block">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4>Error</h4>
	@if(is_array($message)) @foreach ($message as $m) {{ $m }} @endforeach
	@else {{ $message }} @endif
</div>
@endif @if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4>Warning</h4>
	@if(is_array($message)) @foreach ($message as $m) {{ $m }} @endforeach
	@else {{ $message }} @endif
</div>
@endif @if ($message = Session::get('info'))
<div class="alert alert-info alert-block">
	<button type="button" class="close" data-dismiss="alert">&times;</button>
	<h4>Info</h4>
	@if(is_array($message)) @foreach ($message as $m) {{ $m }} @endforeach
	@else {{ $message }} @endif
</div>
@endif

<script>
    @if (Session::has("notification"))
        siravelNotify("{{ Session::get("notification") }}", "{{ Session::get("notificationType") }}");
    @endif

    @if (Session::has("message"))
        siravelNotify("{{ Session::get("message") }}", "alert-info");
    @endif

    @if (Session::has("success"))
        siravelNotify("{{ Session::get("success") }}", "alert-success");
    @endif

    @if (Session::has("errors") && count(Session::get("errors")) >= 1)
        siravelNotify("{!! collect($errors->all())->implode('<br>') !!}", "alert-danger");
    @endif
</script>

@if (Session::has("notification"))
    siravelNotify("{{ Session::get("notification") }}", "{{ Session::get("notificationType") }}");
@endif

@if (Session::has("message"))
    siravelNotify("{{ Session::get("message") }}", "alert-info");
@endif

@if (Session::has("errors"))
    @foreach ($errors->all() as $error)
        siravelNotify("{{ $error }}", "alert-danger");
    @endforeach
@endif