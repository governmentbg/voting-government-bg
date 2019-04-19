@extends('layouts.app')

@section('content')
@if(!auth()->guard('backend')->check())
    @include('partials.public-nav-bar')
@else
    @include('partials.admin-nav-bar')
@endif
@include('components.breadcrumbs')
<div class="row">
	@include('components.status')
    <div class="col-lg-7 p-l-25">
        <div class="p-l-40">
            <h3><b>{{ __('custom.online_voting_system') }}</b></h3>
            <p>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Impedit non nulla porro ratione ipsam itaque, consequatur, facilis,
                omnis quisquam maxime cupiditate repellat delectus quasi fugiat inventore qui possimus dolor? Accusantium.
            </p>
        </div>
    </div>
    <div class="col-lg-5 p-l-40">
        @if(!auth()->guard('backend')->check())
        <div>
            <form method="POST" action="{{route('admin.login')}}">
                {{ csrf_field() }}
                <div class="form-group row">
                    <h3><b>{{ __('custom.login_into_platform') }}</b></h3>
                </div>
                <div class="form-group row m-b-none">
                    <label for="username" class="col-xs-12 col-form-label">{{ __('custom.user_name') }}:</label>
                </div>
                <div class="form-group row">
                    <div class="col-lg-9 p-l-none">
                        <input type="text" class="input-box" name="username">
                        <span class="error">{{ $errors->first('username') }}</span>
                    </div>
                </div>
                <div class="form-group row m-b-none">
                    <label for="password" class="col-xs-12 col-form-label">{{ __('custom.password') }}:</label>
                </div>
                <div class="form-group row">
                <div class="col-lg-9 p-l-none">
                        <input type="password" class="input-box" name="password">
                        <span class="error">{{ $errors->first('password') }}</span>
                    </div>
                </div>
                <div class="form-group row p-t-15">
                    <div class="col-lg-6 col-md-6 p-l-none">
                        <a href="{{ route('password.request') }}">
                            <h3 class="f-s-14">{{ __('custom.forgotten_password') }}</h3>
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 text-right">
                        @include('components.button', ['buttonLabel' => __('custom.login')])
                    </div>
                </div>
            </form>
            <div class="row p-t-15">

            </div>
        </div>
        @endif
    </div>
</div>

