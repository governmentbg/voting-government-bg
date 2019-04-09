@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')

@include('components.breadcrumbs')

<div class="row">
    <div class="col-lg-12">
        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="table table-striped ams-table voting-tours-list">
                    <thead>
                        <tr>
                            <th class="w-20">{{ __('custom.username') }}</th>
                            <th class="w-30">{{ __('custom.own_name') }}</th>
                            <th class="w-25">{{ __('custom.email') }}</th>
                            <th class="w-15">{{ __('custom.status') }}</th>
                            <th class="w-10">{{ __('custom.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <tr>
                            <td>usernamecool1</td>
                            <td class="text-left">Мембер мемберов</td>
                            <td>member@mail.bg</td>
                            <td>{{ __('custom.active') }}</td>
                            <td><a href="#"><img src="{{ asset('img/edit.svg') }}" height="30px" width="50px"/></a></td>
                        </tr>
                        <tr>
                            <td>usernamecool2</td>
                            <td class="text-left">Иван тончев</td>
                            <td>misho@mail.bg</td>
                            <td>{{ __('custom.active') }}</td>
                            <td><a href="#"><img src="{{ asset('img/edit.svg') }}" height="30px" width="50px"/></a></td>
                        </tr>
                        <tr>
                            <td>usernamecool3</td>
                            <td class="text-left">Гошо Гошев</td>
                            <td>gosho@mail.bg</td>
                            <td>{{ __('custom.active') }}</td>
                            <td><a href="#"><img src="{{ asset('img/edit.svg') }}" height="30px" width="50px"/></a></td>
                        </tr>
                        <tr>
                            <td>usernamecool111</td>
                            <td class="text-left">Галин Начев</td>
                            <td>tosh@mail.bg</td>
                            <td>{{ __('custom.active') }}</td>
                            <td><a href="#"><img src="{{ asset('img/edit.svg') }}" height="30px" width="30px"/></a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-12 text-right">
        @include('components.button', ['buttonLabel' => __('custom.add_new_member')])
    </div>
</div>
@endsection
