@extends('layouts.mail')

@section('content')
{{__('custom.greetings')}}, <br><br>

{{ trans('messages.reset_password_email1') }} <br>
{{ trans('messages.reset_password_email2') }} <br><br>

<a href="{{ url(config('app.url').route('password.reset', $token, false)) }}"> {{ trans('custom.password_change') }}</a>
@endsection
