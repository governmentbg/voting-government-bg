@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')
<div class="container center-flex">
    <div class="col-lg-12 col-md-11 col-xs-12 col-lg-offset-1 m-t-md">
        <div class="row justify-center">
            @include('components.errors')
            <div class="col-md-10">
                <div>
                    <h2 class="color-dark"><b>{{ __('custom.new_member_edit') }}</b></h2>
                </div>
                <form method="POST" class="m-t-20" action="{{ route('admin.committee.update', ['id' => $user->id]) }}">
                    {{ method_field('PUT') }}
                    {{ csrf_field() }}
                    <div class="form-group row required">
                        <label for="username" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.username') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="username"
                                value="{{ $user->username }}"
                                readonly
                            >
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="first_name" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.own_name') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="first_name"
                                value="{{ $user->first_name }}"
                            >
                            <span class="error">{{ $errors->first('first_name') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="last_name" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.family_name') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="last_name"
                                value="{{ $user->last_name }}"
                            >
                            <span class="error">{{ $errors->first('last_name') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="email" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.email') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="email"
                                class="input-box"
                                name="email"
                                value="{{ $user->email }}"
                            >
                            <span class="error">{{ $errors->first('email') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.active') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            @include('components.checkbox', ['name' => 'active', 'checked' => (bool)$user->active])
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.last_change_time') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none p-t-10">
                            {{ date('Y-m-d H:i:s', strtotime($user->updated_at)) }}
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.last_change_user') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none p-t-10">
                            {{ $user->updated_by_username }}
                        </div>
                    </div>
                    <div class="form-group row required">
                        <div class="col-sm-12 col-xs-6 p-r-none text-right">
                            @include('components.button', ['buttonLabel' => __('custom.send')])
                            <a class="btn btn-primary login-btn p-t-13" href="{{ route('admin.committee.list') }}">{{ __('custom.back') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
