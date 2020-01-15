@extends('layouts.mail')

@section('content')
{{__('custom.greetings')}}, <br><br>

{{ trans('messages.reset_password_email1') }} <br>
{{ trans('messages.reset_password_email2') }} <a href="{{ url(config('app.url').route('password.reset', $token, false)) }}"> {{ uptrans('custom.password_change') }}</a>
@endsection
