@extends('layouts.mail')

@section('content')

{{ __('custom.greetings') }}, {{ $name }},<br><br>

{{ __('custom.register_account') }}<br><br>

{{ __('custom.username') }}: {{ $username }}<br>
{{ __('custom.password') }}: {{ $password }}<br><br>

<a href="{{ isset($isAdmin) && $isAdmin ? route('admin.index') : route('home') }}"> {{ __('custom.login_into_platform') }}</a>

@endsection
