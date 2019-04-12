@extends('layouts.app')

@php
    if(request()->segment(1) == 'admin'){
        $changePasswordRoute = route('admin.change_password');
        $registeredOrgs = route('admin.voting_tour.list');
        $route = route('admin.org_list');
    }
    else {
        $changePasswordRoute = route('organisation.change_password');
        $registeredOrgs='';
        $route = route('organisation.view');
    }
@endphp

@section('content')
@if(request()->segment(1) == 'admin')
    @include('partials.admin-nav-bar')
@else
    @include('partials.user-nav-bar')
@endif
@php
    $breadcrumbs[] = (object) ['label' => 'Начало', 'link' => $route];
    $breadcrumbs[] = (object) ['label' => 'Настройки'];
@endphp
@include('components.breadcrumbs')

<div class="container center-flex">
    <div class="col-lg-12 col-md-11 col-xs-12 col-lg-offset-1 m-t-md">
        <div class="row justify-center">
            <div class="col-md-10">
                <div>
                    <h2 class="color-dark"><b>{{ __('custom.settings') }}</b></h2>
                </div>
                <div class="form-group row">
                    <div class="col-sm-8">
                        <a href="{{$changePasswordRoute}}">{{__('custom.password_change')}}</a>
                    </div>
                </div>
                @if (request()->segment(1) == 'admin')
                <div class="form-group row">
                    <div class="col-sm-8">
                        <a href="{{$registeredOrgs}}">{{__('custom.elections')}}</a>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
