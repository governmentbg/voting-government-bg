@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')
<div class="container center-flex">
    <div class="col-lg-12 col-md-11 col-xs-12 col-lg-offset-1 m-t-md">
        <div class="row justify-center">
            <div class="col-md-10">
                <div>
                    <h2 class="color-dark"><b>{{ __('custom.new_member_edit') }}</b></h2>
                </div>
                <form method="POST" class="m-t-20">
                    {{ csrf_field() }}
                    <div class="form-group row required">
                        <label for="username" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.username') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="username"
                                value="john_atanasov"
                                readonly
                            >
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="own_name" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.own_name') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="own_name"
                                value=""
                            >
                            <span class="error">{{ $errors->first('own_name') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="family_name" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.family_name') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="family_name"
                                value=""
                            >
                            <span class="error">{{ $errors->first('family_name') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="email" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.email') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="email"
                                class="input-box"
                                name="email"
                                value=""
                            >
                            <span class="error">{{ $errors->first('email') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.active') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            @include('components.checkbox', ['name' => 'active'])
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.last_change_time') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none p-t-10">
                            2018-10-10
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.last_change_user') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none p-t-10">
                            system
                        </div>
                    </div>
                    <div class="form-group row required">
                        <div class="col-sm-12 col-xs-6 p-r-none text-right">
                            @include('components.button', ['buttonLabel' => __('custom.send')])
                            @include('components.button', ['buttonLabel' => __('custom.back')])
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
