@extends('layouts.app')

@section('content')
@if(!auth()->check())
    @include('partials.public-nav-bar')
@else
    @include('partials.user-nav-bar')
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
        @if(!auth()->check() && !isset($reset_password))
        <div>
            <form method="POST" action="{{route('login')}}">
                {{ csrf_field() }}
                <div class="form-group row">
                    <h3><b>{{ __('custom.login_into_platform') }}</b></h3>
                </div>
                <div class="form-group row m-b-none">
                    <label for="username" class="col-xs-12">{{ __('custom.user_name') }}:</label>
                </div>
                <div class="form-group row">
                    <div class="col-lg-9 p-l-none">
                        <input type="text" class="input-box" name="username" value="{{old('username')}}">
                        <span class="error">{{ $errors->first('username') }}</span>
                    </div>
                </div>
                <div class="form-group row m-b-none">
                    <label for="password" class="col-xs-12">{{ __('custom.password') }}:</label>
                </div>
                <div class="form-group row">
                <div class="col-lg-9 p-l-none">
                        <input type="password" class="input-box" name="password" autocomplete="off">
                        <span class="error">{{ $errors->first('password') }}</span>
                    </div>
                </div>
                <div class="form-group row p-t-15">
                    <div class="col-lg-9 text-right">
                        @include('components.button', ['buttonLabel' => __('custom.login')])
                    </div>
                </div>
            </form>
            <div class="row p-t-15">
                <div class="col-lg-4 p-l-none p-r-none">
                    <a
                        href="{{ route('organisation.register') }}"
                    ><h3 class="f-s-14">{{ __('custom.register') }}</h3></a>
                </div>
                <div class="col-lg-5 text-right p-l-none">
                    <a href="{{ route('password.request') }}">
                        <h3 class="f-s-14">{{ __('custom.forgotten_password') }}</h3>
                    </a>
                </div>
            </div>
            <div class="form-group row text-center p-t-15">
                <div class="col-xs-12">
                    <a href="mailto:{{config('mail.MAILTO')}}"><h3 class="f-s-14">{{ __('custom.contact_committee') }}</h3></a>
                </div>
            </div>
        </div>
        @elseif(!auth()->check())
            <!-- Forgotten password form -->
            <form method="POST" action="{{ route('password.email') }}">
                {{ csrf_field() }}
                <div class="form-group row">
                    <div class="col-lg-9">@include('components.errors')</div>
                    <div class="col-lg-9">@include('components.status')</div>
                </div>
                <div class="form-group row">
                    <h3><b>{{ __('custom.forgotten_password') }}</b></h3>
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
                    <label for="email" class="col-xs-12 col-form-label">{{ __('custom.email') }}:</label>
                </div>
                <div class="form-group row">
                    <div class="col-lg-9 p-l-none">
                        <input type="text" class="input-box" name="email">
                        <span class="error">{{ $errors->first('email') }}</span>
                    </div>
                </div>
                <div class="form-group row p-t-15">
                    <div class="col-lg-4 text-right p-l-none">
                        <a href="{{ route('home') }}">
                            <h3 class="f-s-14">{{ __('custom.login') }}</h3>
                        </a>
                    </div>
                    <div class="col-lg-5 text-right">
                        @include('components.button', ['buttonLabel' => __('custom.send')])
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>

<hr class="hr-thin">

<div class="col-lg-12">
    <div class="col-lg-6 inline-block">
        <div class="p-l-60">
            <div><h3 class="p-b-15"><b>{{ __('custom.registered') }}</b></h3></div>
            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="table table-striped ams-table">
                        <thead>
                            <tr>
                                <th class="w-50">{{ __('custom.organisation') }}</th>
                                <th class="w-5">{{ __('custom.candidate') }}</th>
                                <th class="w-15">{{ __('custom.eik') }}</th>
                                <th class="w-30">{{ __('custom.registered_at') }}</th>
                        </tr>
                        </thead>
                        <tbody class="text-center">
                            <tr>
                                <td><img src="{{ asset('img/view.svg') }}" height="30px" width="30px" class="p-r-5"/>Organisation 1</td>
                                <td class="text-center"> <img src="{{ asset('img/tick.svg') }}" height="30px" width="30px" /></td>
                                <td>123123123123</td>
                                <td>2019-03-03 </td>
                            </tr>

                            <tr>
                                <td><img src="{{ asset('img/view.svg') }}" height="30px" width="30px" class="p-r-5"/>Organisation 2</td>
                                <td class="text-center"> <img src="{{ asset('img/tick.svg') }}" height="30px" width="30px" /></td>
                                <td>123123123123</td>
                                <td>2019-03-03 </td>
                            </tr>
                            <tr>
                                <td><img src="{{ asset('img/view.svg') }}" height="30px" width="30px" class="p-r-5"/>Organisation 3</td>
                                <td></td>
                                <td>123123123123</td>
                                <td>2019-03-03 </td>
                            </tr>
                            <tr>
                                <td><img src="{{ asset('img/view.svg') }}" height="30px" width="30px" class="p-r-5"/>Organisation 4</td>
                                <td class="text-center"> <img src="{{ asset('img/tick.svg') }}" height="30px" width="30px" /></td>
                                <td>123123123123</td>
                                <td>2019-03-03 </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 inline-block">
        <div class="v-align-top">
            <table>
                <thead>
                    <tr>
                        <th width="55%">{{ __('custom.organisationNameData') }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ __('custom.name') }}</td><td>Organisation 1</td>
                    </tr>
                    <tr>
                        <td>{{ __('custom.eik') }}</td><td>123123123123</td>
                    </tr>
                    <tr>
                        <td>{{ __('custom.address') }}</td><td>ул. Незабравка 123</td>
                    </tr>
                    <tr>
                        <td>{{ __('custom.representative') }}</td><td>Ivan Ivanov</td>
                    </tr>
                    <tr>
                        <td>{{ __('custom.reg_date') }}</td><td>2019-03-03 </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
