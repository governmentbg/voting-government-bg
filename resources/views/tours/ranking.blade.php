@extends('layouts.app')

@section('content')
    @include('partials.admin-nav-bar')
    @include('components.breadcrumbs')

    <div class="row">
        @include('components.errors')
        @include('components.status')
        <div class="col-md-10 offset-md-1">
        @include('partials.public-ranking')
        </div>
    </div>

@endsection