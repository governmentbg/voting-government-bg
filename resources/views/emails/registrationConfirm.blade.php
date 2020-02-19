@extends('layouts.mail')

@section('content')

{{ __('custom.greetings') }}, {{ $name }},<br><br>

{{ __('custom.register_account') }}<br><br>
<b>
    {{ __('custom.username') }}: {{ $username }}<br>
    {{ __('custom.password') }}: {{ $password }}<br><br>
</b>

{{ __('custom.important_bold') }}<b> {{ __('custom.reg_important') }}</b><br><br>

{{ __('custom.enter_to_see_status') }} :<br><br>

<b> {{ __('custom.status_types') }}: </b><br>
<b> • {{ '"'. __('custom.org_status_new') .'"' }}</b> - {{ __('custom.stat_new_explain') }};<br>
<b> • {{ '"'. __('custom.org_status_participant') .'"' }}</b> - {{ __('custom.stat_part_explain') }};<br>
<b> • {{ '"'. __('custom.org_status_candidate') .'"' }}</b> - {{ __('custom.stat_candidate_explain') }};<br>
<b> • {{ '"'. __('custom.org_status_rejected') .'"' }}</b> - {{ __('custom.stat_rejected_explain') }}<br><br>

{{ __('custom.public_important_one') }} {!! __('custom.public_important_two') !!} {{ __('custom.public_important_three') }}<br><br>

{{ __('custom.to_login_please_use') }} <a href="{{ isset($isAdmin) && $isAdmin ? route('admin.index') : route('home') }}"> {{ __('custom.login_into_platform') }}</a>

@endsection
