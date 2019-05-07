@extends('layouts.mail')

@section('content')
{{__('custom.greetings')}}, {{ $name }} <br><br>
Регистрирани сте за участие в изборите за членове на Съвета за развитие на гражданско общество.
За да упражните правото си на вот, моля използвайте линка отдолу.
<a href="{{ route('organisation.vote') }}" style="text-decoration: none;">{{__('custom.vote')}}</a>
@endsection
