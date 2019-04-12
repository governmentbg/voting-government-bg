@extends('layouts.app')

@section('content')
@include('partials.public-nav-bar')

<div class="row text-center">
    <div class="col-lg-4 p-l-40 offset-lg-4">
        @if(!auth()->guard('backend')->check())
        <div>
            <form method="POST" action="{{route('password.reset', ['token' => null])}}">
                {{ csrf_field() }}
                @include('components.errors')
                @include('components.status')
                <input type="hidden" value="{{ $token }}" name="token">
                <div class="form-group row">
                    <h3><b>{{ __('custom.forgotten_password') }}</b></h3>
                </div>
                <div class="form-group row m-b-none">
                    <label for="password" class="col-xs-12 col-form-label">{{ __('custom.new_password') }}:</label>
                </div>
                <div class="form-group row">
                    <div class="col-lg-9 p-l-none">
                            <input type="password" class="input-box" name="password">
                            <span class="error">{{ $errors->first('password') }}</span>
                    </div>
                </div>
                <div class="form-group row m-b-none">
                    <label for="password" class="col-xs-12 col-form-label">{{ uctrans('validation.attributes.password_confirm') }}:</label>
                </div>
                <div class="form-group row">
                    <div class="col-lg-9 p-l-none">
                            <input type="password" class="input-box" name="password_confirmation">
                            <span class="error">{{ $errors->first('password_confirmation') }}</span>
                    </div>
                </div>
                <div class="form-group row p-t-15">
                    <div class="col-lg-9 text-right">
                        @include('components.button', ['buttonLabel' => __('custom.confirm')])
                    </div>
                </div>
            </form>
            <div class="row p-t-15">

            </div>
        </div>
        @endif
    </div>
</div>
@endsection


