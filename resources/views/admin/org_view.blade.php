@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')
<div class="row">
    <div class="col-lg-12 p-l-40"><h3>{{ __('custom.data_for') }} Организация 1</h3></div>
</div>
<div class="row m-l-5">
    <div class="col-lg-6">
        <div class="col-md-10">
            {{ csrf_field() }}
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.org_name') }}:</label>
                <div class="col-sm-4">
                    <span>Организация 1</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.eik_bulstat') }}:</label>
                <div class="col-sm-4">
                    <span>123123123</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.management_address') }}:</label>
                <div class="col-sm-4">
                    <span>ул.Незабравка 123</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.representative') }}:</label>
                <div class="col-sm-4">
                    <span>Иван Иванов Иванов</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.registered_at') }}:</label>
                <div class="col-sm-4">
                    <span>01 Януари 2019</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.phone_number') }}:</label>
                <div class="col-sm-4">
                    <span>1231231231</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.email') }}:</label>
                <div class="col-sm-4">
                    <span>ivan@ivan.bg</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.in_ap') }}:</label>
                <div class="col-sm-4">
                    @include('components.checkbox', ['name' => 'test'])
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.candidate') }}:</label>
                <div class="col-sm-4">
                    @include('components.checkbox', ['name' => 'test'])
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="col-md-10">
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12">{{ __('custom.experience_info') }}:</label>
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
                <label class="col-sm-4 col-xs-12">{{ __('custom.reference_materials') }}:</label>
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
                <label class="col-sm-4 col-xs-1">{{ __('custom.status') }}:</label>
                <div class="col-sm-8 col-xs-6 p-r-none">
                    <h3 class="display-inline">Потвърден кандидат</h3> <img src="{{ asset('img/tick.svg') }}" height="30px" width="30px" class="display-inline m-t-12"/>
                </div>
            </div>
        </div>
    </div>
</div>

<hr class="hr-thin">

<div class="row">
    <div class="col-lg-12 p-l-40"><h2>{{ __('custom.messages') }}</h2></div>
</div>
<div class="col-lg-12">
    <div class="col-lg-12">
        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="table table-striped ams-table">
                    <thead>
                        <tr>
                            <th class="w-50">{{ __('custom.title') }}</th>
                            <th class="w-30">{{ __('custom.date') }}</th>
                            <th class="w-20">{{ __('custom.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><img src="{{ asset('img/circle-fill.svg') }}" height="30px" width="30px" class="p-r-5"/>Съобщение 1 </td>
                            <td class="text-center">2019-04-08</td>
                            <td class="text-center"><a href="#"><img src="{{ asset('img/view.svg') }}" height="30px" width="30px" class="p-r-5"/></a></td>
                        </tr>

                        <tr>
                            <td><img src="{{ asset('img/circle-fill.svg') }}" height="30px" width="30px" class="p-r-5"/>Съобщение 2</td>
                            <td class="text-center">2019-04-08</td>
                            <td class="text-center"><a href="#"><img src="{{ asset('img/view.svg') }}" height="30px" width="30px" class="p-r-5"/></a></td>
                        </tr>
                        <tr>
                            <td><img src="{{ asset('img/circle-no-fill.svg') }}" height="30px" width="30px" class="p-r-5"/>Съобщение 3</td>
                            <td class="text-center">2019-04-08</td>
                            <td class="text-center"><a href="#"><img src="{{ asset('img/view.svg') }}" height="30px" width="30px" class="p-r-5"/></a></td>
                        </tr>
                        <tr>
                            <td><img src="{{ asset('img/circle-fill.svg') }}" height="30px" width="30px" class="p-r-5"/>Съобщение 4</td>
                            <td class="text-center">2019-04-08</td>
                            <td class="text-center"><a href="#"><img src="{{ asset('img/view.svg') }}" height="30px" width="30px" class="p-r-5"/></a></td>
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

<hr class="hr-thin">

<div class="row">
    <div class="col-lg-12 p-l-40"><h2>{{ __('custom.applied_files') }}</h2></div>
</div>
<div class="col-lg-6">
    <label class="col-md-6 col-xs-12">Удостоверение за удостоверено удостоверение.pdf</label>
    <div class="col-md-6 display-inline">
        <a href="#"><img src="{{ asset('img/download.svg') }}" height="30px" width="30px" class="p-r-5"/></a>
    </div>
</div>
<div class="col-lg-6">
    <label class="col-md-6 col-xs-12">Удостоверение за удостоверено удостоверение.pdf</label>
    <div class="col-md-6 display-inline">
        <a href="#"><img src="{{ asset('img/download.svg') }}" height="30px" width="30px" class="p-r-5"/></a>
    </div>
</div>
</div>
@php
    http2_push_image('/img/tick.svg');
    http2_push_image('/img/circle-fill.svg');
    http2_push_image('/img/circle-no-fill.svg');
    http2_push_image('/img/download.svg');
    http2_push_image('/img/view.svg');
@endphp

@endsection
