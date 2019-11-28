@extends('layouts.mail')

@section('content')
{{__('custom.greetings')}}, {{ $name }}, <br><br>

{{ __('custom.your_vote_was_registered') }}.<br>

{{ __('custom.thank_you_for_participating') }}.<br>
@endsection
