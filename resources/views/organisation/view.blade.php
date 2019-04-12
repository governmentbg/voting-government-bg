@extends('layouts.app')

@section('content')
@include('partials.user-nav-bar')
@include('components.breadcrumbs')
<div class="row">
    <div class="col-lg-12 p-l-40"><h3>{{ __('custom.data_for') }} {{ $organisation->name }}</h3></div>
</div>
<div class="row m-l-5">
    <div class="col-lg-6">
        <div class="col-md-10">
            {{ csrf_field() }}
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.org_name') }}:</label>
                <div class="col-sm-4">
                    <span>{{ $organisation->name }}</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.eik_bulstat') }}:</label>
                <div class="col-sm-4">
                    <span>{{ $organisation->eik }}</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.management_address') }}:</label>
                <div class="col-sm-4">
                    <span>{{ $organisation->address }}</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.representative') }}:</label>
                <div class="col-sm-4">
                    <span>{{ $organisation->representative }}</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.registered_at') }}:</label>
                <div class="col-sm-4">
                    <label>{{ translate_date(date('d F Y', strtotime($organisation->created_at))) }}</label>
                    <span>{{ date('H:i', strtotime($organisation->created_at)) }}</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.phone_number') }}:</label>
                <div class="col-sm-4">
                    <span>{{ $organisation->phone }}</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.email') }}:</label>
                <div class="col-sm-4">
                    <span>{{ $organisation->email }}</span>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.in_av') }}:</label>
                <div class="col-sm-4">
                    @include('components.checkbox', ['readonly' => true, 'checked' => $organisation->in_av])
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-12"> {{ __('custom.candidate') }}:</label>
                <div class="col-sm-4">
                    @include('components.checkbox', ['readonly' => true, 'checked' => $organisation->is_candidate])
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
                        readonly="true"
                    >{{ $organisation->description }}</textarea>
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
                        readonly="true"
                    >{{ $organisation->references }}</textarea>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-4 col-xs-1">{{ __('custom.status') }}:</label>
                <div class="col-sm-8 col-xs-6 p-r-none">
                    <h3 class="display-inline">{{ $status }}</h3> &nbsp;
                    <img src="{{ $isApproved ? asset('img/tick.svg') : asset('img/cross.svg') }}" height="30px" width="30px" class="display-inline m-t-12"/>
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
                @if($messages->isNotEmpty())
                <table class="table table-striped ams-table">
                    <thead>
                        <tr>
                            <th class="w-50">{{ __('custom.title') }}</th>
                            <th class="w-30">{{ __('custom.date') }}</th>
                            <th class="w-20">{{ __('custom.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $message)
                        <tr>
                            <td>
                                <img src="{{ $message->isRead() ? asset('img/circle-fill.svg') : asset('img/circle-no-fill.svg') }}" height="30px" width="30px" class="p-r-5"/>
                                {{ $message->subject }}
                            </td>
                            <td class="text-center">{{ date('Y-m-d', strtotime($message->created_at)) }}</td>
                            <td class="text-center">
                                <a href="{{ route('organisation.messages', ['org_id' => $organisation->id, 'id' => $message->id])}}">
                                    <img src="{{ asset('img/view.svg') }}" height="30px" width="30px" class="p-r-5"/>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>{{-- $messages->links() --}}</tfoot>
                </table>
                @else
                <span>{{ __('messages.no_messages') }}</span>
                @endif
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

<!-- ------------FILES ------------------->
<div class="row">
@if(!empty($files))
    @foreach($files as $file)
        <div class="col-lg-6 p-l-40">
            <label class="col-md-6 col-xs-12">{{ $file->name }}</label>
            <div class="col-md-6 display-inline">
                <a href="{{route('fileDowload', $file->id)}}"><img src="{{ asset('img/download.svg') }}" height="30px" width="30px" class="p-r-5"/></a>
            </div>
        </div>
    @endforeach
@else
<div class="col-lg-12 p-l-40"><span>{{ __('messages.no_files') }}</span></div>
@endif
</div>
@endsection
