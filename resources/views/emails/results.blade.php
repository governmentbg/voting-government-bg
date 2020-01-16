@extends('layouts.mail')

@section('content')
{{ __('custom.greetings') }}, {{ $name }}, <br><br>
{{ __('custom.ranking_for') }} {{ $tourName }} {{ __('custom.was_done') }}. <br>
{{ __('custom.results_available') }}: <a href="{{ route('list.ranking') }}" style="text-decoration: none;">{{uptrans('custom.results')}}</a>
@endsection
