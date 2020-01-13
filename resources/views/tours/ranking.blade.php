@extends('layouts.app')

@section('content')
    @include('partials.admin-nav-bar')
    @include('components.breadcrumbs')

    <div class="row">
        @include('components.status')
        <div class="col-lg-12">
            @include('partials.public-ranking', ['orgNotEditable' => $orgNotEditable])
        </div>
    </div>

@endsection
