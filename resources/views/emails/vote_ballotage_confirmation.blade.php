@extends('layouts.mail')

@section('content')
{{__('custom.greetings')}}, {{ $name }}, <br><br>

{{ __('custom.vote_successful_ballotage_msg') }}<br>

{{ __('custom.thank_you_for_participating') }}<br>
@endsection
