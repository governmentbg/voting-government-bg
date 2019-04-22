@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')
<div class="container center-flex">
    <div class="col-lg-12 col-md-11 col-xs-12 col-lg-offset-1 m-t-md">
        @include('components.errors')
        <div class="row justify-center">
            <div class="col-md-10">
                <div>
                    <h2 class="color-dark"><b>{{ __('custom.new_member_add') }}</b></h2>
                </div>
                <form method="POST" class="m-t-20" action="{{ route('admin.committee.store') }}">
                    {{ csrf_field() }}
                    <div class="form-group row required">
                        <label for="username" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.username') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="username"
                                value="{{ old('username') }}"
                            >
                            <span class="error">{{ $errors->first('username') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="first_name" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.own_name') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="first_name"
                                value="{{ old('first_name') }}"
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
                                value="{{ old('last_name') }}"
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
                                value="{{ old('email') }}"
                            >
                            <span class="error">{{ $errors->first('email') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.active') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            @include('components.checkbox', ['name' => 'active', 'checked' => old('active')])
                        </div>
                    </div>
                    <div class="form-group row required">
                        <div class="col-sm-12 col-xs-6 p-r-none text-right">
                            @include('components.button', ['buttonLabel' => __('custom.send')])
                            <a class="btn btn-primary login-btn" href="{{ route('admin.committee.list') }}">{{ __('custom.back') }}</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
