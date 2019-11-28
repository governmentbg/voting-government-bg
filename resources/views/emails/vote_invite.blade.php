@extends('layouts.mail')

@section('content')
{{__('custom.greetings')}}, {{ $name }}, <br><br>
{{ __('custom.you_are_registered') }}.<br>
{{ __('custom.to_vote_link') }}.<br>
<a href="{{ route('organisation.vote') }}" style="text-decoration: none;">{{__('custom.vote')}}</a>
@endsection
