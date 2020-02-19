@extends('layouts.mail')

@section('content')
{{__('custom.greetings')}}, {{ $name }}, <br><br>
{{ __('custom.you_are_registered') }}.<br>
<u>{{ __('custom.to_vote_link') }} <a href="{{ route('organisation.vote') }}" style="text-decoration: none;">{{ uptrans('custom.vote') }}</a></u><br><br>

<b>{{ __('custom.important_bold') }}</b> {{ __('custom.vote_important_one') }}<br><br>
<b>{{ __('custom.important_bold') }}</b> {{ __('custom.vote_important_two') }}<br><br>
<b>{{ __('custom.important_bold') }}</b> {{ __('custom.vote_important_three') }}<br><br>

@endsection
