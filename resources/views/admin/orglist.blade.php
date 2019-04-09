@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')

<div class="row">
    <div class="col-lg-10">
        <div>
            <img src="{{ asset('img/tick.svg') }}" height="30px" width="30px" class="display-inline m-b-8 p-r-5"/>
            <h3 class="display-inline">Име на изборите за 2019 година</h3>
        </div>
        <div class="row">
            <div class="col-lg-4 display-inline">
                <label for="status" class="text-left">{{__('custom.status')}}:</label>
                <select name="status" class="ams-dropdown custom-select w-50">
                    <option value="0">Виж всички</option>
                    <option value="1">Нов</option>
                    <option value="2">Потвърден</option>
                    <option value="3">Отхвърлен</option>
                </select>
            </div>
            <div class="col-lg-5 display-inline">
                <div class="display-inline ">
                    <label for="eik">{{__('custom.eik')}}:</label>
                </div>
                <input type="text" name="eik" placeholder="{{__('custom.search')}}" class="search-box float-right w-70">
            </div>
            <div class="col-lg-3 display-inline">
                <label for="candidate">{{__('custom.candidate')}}:</label>
                <select name="candidate" class="ams-dropdown custom-select w-50">
                    <option value="0">Виж всички</option>
                    <option value="1">Да</option>
                    <option value="2">Не</option>
                </select>
            </div>
        </div>
        <div class="row m-t-10">
            <div class="offset-lg-4 col-lg-5 display-inline">
                <div class="display-inline">
                    <label for="email">{{__('custom.email')}}:</label>
                </div>
                <input type="text" name="email" placeholder="{{__('custom.search')}}" class="search-box float-right w-70">
            </div>
        </div>
        <div class="row m-t-10">
            <div class="offset-lg-4 col-lg-5 display-inline">
                <div class="display-inline">
                    <label for="org_name">{{__('custom.org_name')}}:</label>
                </div>
                <input type="text" name="org_name" placeholder="{{__('custom.search')}}" class="search-box float-right w-70">
            </div>
        </div>
        <div class="row m-t-10">
            <div class="offset-lg-4 col-lg-6 display-inline">
                <div class="display-inline">
                    <label for="registered_period">{{__('custom.registered_period')}}:</label>
                </div>
                <div class="display-inline float-right col-lg-9 p-l-none">
                    <input type="text" name="registered_period" class="date-box"><img src="{{ asset('img/calendar.svg') }}" height="30px" width="30px" class="m-r-10"/>
                    <input type="text" name="registered_period" class="date-box"><img src="{{ asset('img/calendar.svg') }}" height="30px" width="30px"/>
                </div>
            </div>
        </div>
        <div class="table-wrapper m-t-20">
            <div class="table-responsive">
                <table class="table table-striped ams-table voting-tours-list">
                    <thead>
                        <tr>
                            <th class="w-20">{{ __('custom.organisation') }}</th>
                            <th class="w-10">{{ __('custom.eik') }}</th>
                            <th class="w-10">{{ __('custom.status') }}</th>
                            <th class="w-10">{{ __('custom.candidate') }}</th>
                            <th class="w-25">{{ __('custom.registered_at') }}</th>
                            <th class="w-15">{{ __('custom.email') }}</th>
                            <th class="w-10">{{ __('custom.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <tr>
                            <td>Организация 1</td>
                            <td class="text-left">12312311</td>
                            <td>{{__('custom.new')}}</td>
                            <td>@include('components.checkbox', ['checked' => true, 'readonly' => true, 'name' => 'holder'])</td>
                            <td>2011-03-03 12:15</td>
                            <td>dragan@mail.bg</td>
                            <td>
                                <a href="#"><img src="{{ asset('img/view.svg') }}" height="30px" width="30px"/></a>
                                <a href="#" class="p-l-25"><img src="{{ asset('img/edit.svg') }}" height="30px" width="30px"/></a>
                            </td>
                        </tr>
                        <tr>
                            <td>Организация 2</td>
                            <td class="text-left">123123123</td>
                            <td>{{__('custom.confirmed')}}</td>
                            <td>@include('components.checkbox', ['checked' => true, 'readonly' => true, 'name' => 'holder'])</td>
                            <td>2014-03-03 12:15</td>
                            <td>ivan@mail.bg</td>
                            <td>
                                <a href="#"><img src="{{ asset('img/view.svg') }}" height="30px" width="30px"/></a>
                                <a href="#" class="p-l-25"><img src="{{ asset('img/edit.svg') }}" height="30px" width="30px"/></a>
                            </td>
                        </tr>
                        <tr>
                            <td>Организация 3</td>
                            <td class="text-left">123123123123</td>
                            <td>{{__('custom.denied')}}</td>
                            <td></td>
                            <td>2017-03-03 12:15</td>
                            <td>tosho@mail.bg</td>
                            <td>
                                <a href="#"><img src="{{ asset('img/view.svg') }}" height="30px" width="30px"/></a>
                                <a href="#" class="p-l-25"><img src="{{ asset('img/edit.svg') }}" height="30px" width="30px"/></a>
                            </td>
                        </tr>
                        <tr>
                            <td>Организация 4</td>
                            <td class="text-left">123123123123</td>
                            <td>{{__('custom.new')}}</td>
                            <td></td>
                            <td>2021-03-03 12:15</td>
                            <td>gosho@mail.bg</td>
                            <td>
                                <a href="#"><img src="{{ asset('img/view.svg') }}" height="30px" width="30px"/></a>
                                <a href="#" class="p-l-25"><img src="{{ asset('img/edit.svg') }}" height="30px" width="30px"/></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
