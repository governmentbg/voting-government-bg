@extends('layouts.mail')

@section('content')

{{ __('custom.greetings') }}, {{ $name }},<br><br>

{{ __('custom.register_account') }}<br><br>
<b>
    {{ __('custom.username') }}: {{ $username }}<br>
    {{ __('custom.password') }}: {{ $password }}<br><br>
</b>
{{ __('custom.to_login_please_use') }} <a href="{{ isset($isAdmin) && $isAdmin ? route('admin.index') : route('home') }}"> {{ __('custom.login_into_platform') }}</a>

@endsection
