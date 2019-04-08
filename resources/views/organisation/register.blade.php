@extends('layouts.app')

@section('content')
@include('partials.public-nav-bar')

<div class="container center-flex">
    <div class="col-lg-12 col-md-11 col-xs-12 col-lg-offset-1 m-t-md">
        <div class="row justify-center">
            <div class="col-md-10">
                <div>
                    <h2 class="color-dark"><b>{{ __('custom.register_clean') }}</b></h2>
                    <p class='req-fields m-t-lg m-b-lg'>{{ __('custom.general_info') }}</p>
                </div>
                <form method="POST" class="m-t-lg p-sm">
                    {{ csrf_field() }}
                    <div class="form-group row required">
                        <label for="fname" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.org_name') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="org_name"
                                value=""
                            >
                            <span class="error">{{ $errors->first('firstname') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="lname" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.eik_bulstat') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="eik"
                                value=""
                            >
                            <span class="error">{{ $errors->first('lastname') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="email" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.management_address') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="email"
                                class="input-box"
                                name="address"
                                value=""
                            >
                            <span class="error">{{ $errors->first('email') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="username" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.representative') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="representative"
                                value=""
                            >
                            <span class="error">{{ $errors->first('username') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="password" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.in_ap') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="in_ap"
                                value=""
                            >
                            <span class="error">{{ $errors->first('password') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="password-confirm" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.phone_number') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="phone_number"
                            >
                            <span class="error">{{ $errors->first('phone_number') }}</span>
                        </div>
                    </div>
                    <div class="form-group row required">
                        <label for="description" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.email') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="email"
                            >
                            <span class="error">{{ $errors->first('email') }}</span>
                        </div>
                    </div>
                </div>
                    <hr class="hr-thin">
                <div class="col-md-10">
                    <h5>{{ __('custom.committee_member_request') }}</h5>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.request_for_candidacy') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            <input
                                type="checkbox"
                                class=""
                                name=""
                            >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.experience_info') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            <textarea
                                class="txt-area"
                                name=""
                                rows="3"
                                cols="40"
                            ></textarea>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12 col-form-label">{{ __('custom.reference_materials') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            <textarea
                                class="txt-area"
                                name=""
                                rows="3"
                                cols="40"
                            ></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<hr class="hr-thin">

@endsection
