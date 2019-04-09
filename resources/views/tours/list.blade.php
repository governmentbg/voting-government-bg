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
                        <th class="w-15">{{ __('custom.status') }}</th>
                        <th class="w-50">{{ __('custom.org_name') }}</th>
                        <th class="w-20">{{ __('custom.end_date') }}</th>
                        <th class="w-15">{{ __('custom.operations') }}</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <tr>
                        <td><img src="{{ asset('img/cross.svg') }}" height="30px" width="30px"/></td>
                        <td class="text-left">Избори за комитет 1 година</td>
                        <td>2011-03-03</td>
                        <td><a href="#"><img src="{{ asset('img/star.svg') }}" height="30px" width="50px"/></a></td>
                    </tr>
                    <tr>
                        <td><img src="{{ asset('img/cross.svg') }}" height="30px" width="30px"/></td>
                        <td class="text-left">Избори за комитет 4 година</td>
                        <td>2014-03-03</td>
                        <td><a href="#"><img src="{{ asset('img/star.svg') }}" height="30px" width="50px"/></a></td>
                    </tr>
                    <tr>
                        <td><img src="{{ asset('img/cross.svg') }}" height="30px" width="30px"/></td>
                        <td class="text-left">Избори за комитет 7 година</td>
                        <td>2017-03-03</td>
                        <td><a href="#"><img src="{{ asset('img/star.svg') }}" height="30px" width="50px"/></a></td>
                    </tr>
                    <tr>
                        <td><img src="{{ asset('img/tick.svg') }}" height="30px" width="30px"/></td>
                        <td class="text-left">Избори за комитет 10 година</td>
                        <td>2021-03-03</td>
                        <td><a href="#"><img src="{{ asset('img/edit.svg') }}" height="30px" width="30px"/></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
        </div> 
    </div>
    <div class="col-lg-12 text-right">
        <button
            type="submit"
            class="btn btn-primary login-btn"
        >{{ __('custom.new_message') }}</button>
    </div>
</div>
@endsection
