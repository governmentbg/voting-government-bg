@extends('layouts.mail')

@section('content')
{{__('custom.greetings')}}, {{ $name }}, <br><br>

Вашият вот беше регистриран.<br>

Благодарим Ви, че участвахте в изборите за членове на Съвета за развитие на гражданско общество.<br>
@endsection
