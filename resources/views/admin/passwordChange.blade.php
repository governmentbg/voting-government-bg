@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')

<div class="container center-flex">
    <div class="col-lg-12 col-md-11 col-xs-12 col-lg-offset-1 m-t-md">
        <div class="row justify-center">
            <div class="col-md-10">
                <div>
                    <h2 class="color-dark"><b>{{ __('custom.password_change') }}</b></h2>
                </div>
                <form method="POST" class="m-t-20">
                    {{ csrf_field() }}
                    <div class="form-group row required">
                        <label for="old_password" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.old_password') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="password"
                                class="input-box"
                                name="old_password"
                                value=""
                            >
                            <span class="error">{{ $errors->first('old_password') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="new_password" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.new_password') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="password"
                                class="input-box"
                                name="new_password"
                                value=""
                            >
                            <span class="error">{{ $errors->first('new_password') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="new_password_repeat" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.new_password_repeat') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="password"
                                class="input-box"
                                name="new_password_repeat"
                                value=""
                            >
                            <span class="error">{{ $errors->first('new_password_repeat') }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12 col-xs-6 p-r-none text-right">
                            @include('components.button', ['buttonLabel' => __('custom.save')])
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
