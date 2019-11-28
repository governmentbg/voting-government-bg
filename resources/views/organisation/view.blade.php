@extends('layouts.app')

@section('content')
@include('partials.user-nav-bar')
@include('components.breadcrumbs')

@include('components.status')
@if (!empty($organisation))
    <div class="row m-b-1r">
        <div class="col-lg-12 p-l-40"><h3>{{ __('custom.data_for') }} {{ $organisation->name }}</h3></div>
    </div>
    <div class="row m-l-5">
        <div class="col-lg-6">
            <div class="col-md-10">
                {{ csrf_field() }}
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12">{{ __('custom.eik_bulstat') }}:</label>
                    <div class="col-sm-4">
                        <span>{{ $organisation->eik }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12">{{ __('custom.org_name') }}:</label>
                    <div class="col-sm-4">
                        <span>{{ $organisation->name }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12">{{ __('custom.management_address') }}:</label>
                    <div class="col-sm-4">
                        <span>{{ $organisation->address }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12">{{ __('custom.representative') }}:</label>
                    <div class="col-sm-4">
                        <span>{{ $organisation->representative }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12">{{ __('custom.registered_at') }}:</label>
                    <div class="col-sm-4">
                        <label>{{ translate_date(date('d F Y', strtotime($organisation->created_at))) }}</label>
                        <span>{{ date('H:i', strtotime($organisation->created_at)) }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12">{{ __('custom.phone_number') }}:</label>
                    <div class="col-sm-4">
                        <span>{{ $organisation->phone }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12" title="{{ __('custom.email_hint') }}">{{ __('custom.email') }}:</label>
                    <div class="col-sm-4">
                        <span>{{ $organisation->email }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12" title="{{ __('custom.av_hint') }}">{{ __('custom.in_av') }}:</label>
                    <div class="col-sm-4">
                        @include('components.checkbox', ['readonly' => true, 'checked' => $organisation->in_av])
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12">{{ __('custom.candidate') }}:</label>
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
                        <div class="nano txt-area-height">
                            <textarea
                                class="txt-area no-outline p-a-5 nano-content"
                                name=""
                                rows="5"
                                cols="40"
                                readonly="true"
                            >{{ $organisation->description }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12">{{ __('custom.reference_materials') }}:</label>
                    <div class="col-sm-8 col-xs-6 p-r-none">
                        <div class="nano txt-area-height">
                            <textarea
                                class="txt-area no-outline p-a-5 nano-content"
                                name=""
                                rows="5"
                                cols="40"
                                readonly="true"
                            >{{ $organisation->references }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-1">{{ __('custom.status') }}:</label>
                    <div class="col-sm-8 col-xs-6 p-r-none">
                        <h3 class="display-inline">{{ $status }}</h3> &nbsp;
                        @if ($isApproved)
                        <img src="{{asset('img/tick.svg')}}" height="30px" width="30px" class="display-inline m-t-12"/>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr class="hr-thin">

    <!-- ------------MESSAGES ------------------->
    <div class="row msg-m-b">
        <div class="col-lg-6 p-l-40"><h2>{{ __('custom.messages') }}</h2></div>
        <div class="col-lg-6 text-right p-r-50">
        @if (empty($messages))
            <a
                href="{{ route('organisation.messages.add') }}"
                class="btn btn-primary login-btn"
            >{{ __('custom.new_message') }}</a>
        @endif
        </div>
    </div>
    @if (!empty($messages))
    <div class="col-lg-12">
        <div class="col-lg-12">
            <div class="table-wrapper">
                <div class="table-responsive">
                    <table class="table table-striped ams-table messages-list">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="w-50">{{ __('custom.title') }}</th>
                                <th class="w-30">{{ __('custom.date') }}</th>
                                <th class="w-20">{{ __('custom.operations') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($messages as $message)
                            <tr class="{{ !is_null($message->sender_user_id) && !$message->read ? 'message-not-read' : ''}}">
                                <td>
                                    <img src="{{ asset('img/arrow.svg') }}"
                                        height="30px" width="30px" class="p-r-5 {{ $message->recipient_org_id == null ? 'rotate-180' : ''}}"/>
                                </td>
                                <td>
                                    <img src="{{ !is_null($message->sender_user_id) && !$message->read ? asset('img/circle-fill.svg') : asset('img/circle-no-fill.svg') }}"
                                        height="30px" width="30px" class="p-r-5"/>
                                    {{ $message->subject }}
                                </td>
                                <td class="text-center">{{ $message->created_at }}</td>
                                <td class="text-center">
                                    <a href="{{ route('organisation.messages', [
                                            'id' => $message->parent_id  ? $message->parent_id : $message->id
                                        ]) . ($message->parent_id ? '#'. $message->id : '') }}">
                                        <img
                                            src="{{ asset('img/view.svg') }}"
                                            title="{{ __('custom.view') }}"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            height="30px"
                                            width="30px"
                                            class="p-r-5"
                                        />
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="display-flex justify-center">
                @if (!empty($messages))
                    {{ $messages->links() }}
                @endif
            </div>
        </div>
        <div class="col-lg-12 text-right">
            <a
                href="{{ route('organisation.messages.add') }}"
                class="btn btn-primary login-btn"
            >{{ __('custom.new_message') }}</a>
        </div>
    </div>
    @elseif (!empty($errors) && $errors->has('msg_message'))
    <div class="row msg-m-b">
        <div class="col-lg-12 p-l-40 alert-error">
            <span>{{ $errors->first('msg_message') }}</span>
        </div>
    </div>
    @endif

    <hr class="hr-thin">

    <!-- ------------FILES ------------------->
    <div class="row">
        <div class="col-lg-12 p-l-40"><h2>{{ __('custom.applied_files') }}</h2></div>
    </div>

    <div class="row p-b-30">
    @if (!empty($files))
        @foreach ($files as $file)
            <div class="col-lg-8 p-l-40">
                <label class="col-md-6 col-xs-12">{{ $file->name }}</label>
                <div class="col-md-6 display-inline">
                    <a href="{{ route('fileDowload', $file->id) }}">
                        <img src="{{ asset('img/download.svg') }}" height="30px" width="30px" class="p-r-5"/>
                    </a>
                </div>
            </div>
        @endforeach
    @elseif (!empty($errors) && $errors->has('files_message'))
        <div class="col-lg-12 p-l-40 alert-error">
            <span>{{ $errors->first('files_message') }}</span>
        </div>
    @else
        <div class="col-lg-12 p-l-40">
            <span>{{ __('messages.no_files') }}</span>
        </div>
    @endif
    </div>
@endif

@endsection
