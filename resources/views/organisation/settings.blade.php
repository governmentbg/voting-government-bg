@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
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
                        <a href="/admin/passwordChange">{{__('custom.password_change')}}</a>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-8">
                        <a href="/list">{{__('custom.elections')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
