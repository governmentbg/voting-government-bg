@extends('layouts.mail')

@section('title')
{{__('custom.vote_invite')}}
@endsection
@section('content')
{{__('custom.greetings')}}, {{ $name }} <br><br>
Lorem ipsum dolor sit, amet consectetur adipisicing elit.
Pariatur numquam ut vitae quod quis tenetur maiores porro necessitatibus dolorum ullam, cumque ratione non accusantium.
Ducimus enim nulla aut et molestias? Lorem ipsum dolor sit amet consectetur adipisicing elit.
Tenetur soluta quam praesentium dolores, iusto porro laudantium consequuntur quae provident quis numquam eius modi, quia unde architecto saepe omnis ipsa cum.
<a href="{{ route('organisation.vote') }}" style="text-decoration: none;">{{__('custom.vote_link')}}</a>
@endsection
