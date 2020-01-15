@extends('layouts.mail')

@section('content')
{{__('custom.greetings')}}, {{ $name }}, <br><br>
{{ __('custom.you_are_registered') }}.<br>
{{ __('custom.to_vote_link') }} <a href="{{ route('organisation.vote') }}" style="text-decoration: none;">{{ uptrans('custom.vote') }}</a>
@endsection
