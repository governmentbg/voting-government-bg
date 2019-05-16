@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')

@include('components.status')
@if (!empty($orgData))
    <div class="row">
        <div class="col-lg-12 p-l-40 m-b-30"><h3>{{ __('custom.data_for') }} {{ $orgData->name }}</h3></div>
    </div>
    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.org_update', ['id' => $orgData->id]) }}">
        <div class="row m-l-5">
            <div class="col-lg-6">
                <div class="col-md-10">
                    {{ csrf_field() }}
                    @include('components.errors')
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12">{{ __('custom.eik_bulstat') }}:</label>
                        <div class="row w-70">
                            <div class="col-lg-12">
                                <input type="text" class="input-box" name="eik" value="{{ $orgData->eik }}" disabled>
                                <span class="error">{{ $errors->first('eik') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12">{{ __('custom.org_name') }}:</label>
                        <div class="row w-70">
                            <div class="col-lg-12">
                                <input
                                    type="text"
                                    class="input-box"
                                    name="name"
                                    value="{{ old('name', $orgData->name) }}"
                                    maxlength="255"
                                    required
                                    title="{{ __('custom.required_mgs', ['field' => ultrans('custom.org_name')]) }}"
                                >
                                <span class="error">{{ $errors->first('name') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12">{{ __('custom.management_address') }}:</label>
                        <div class="row w-70">
                            <div class="col-lg-12">
                                <input
                                    type="text"
                                    class="input-box"
                                    name="address"
                                    value="{{ old('address', $orgData->address) }}"
                                    maxlength="512"
                                    required
                                    title="{{ __('custom.required_mgs', ['field' => ultrans('custom.management_address')]) }}"
                                >
                                <span class="error">{{ $errors->first('address') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12">{{ __('custom.representative') }}:</label>
                        <div class="row w-70">
                            <div class="col-lg-12">
                                <input
                                    type="text"
                                    class="input-box"
                                    name="representative"
                                    value="{{ old('representative', $orgData->representative) }}"
                                    maxlength="512"
                                    required
                                    title="{{ __('custom.required_mgs', ['field' => ultrans('custom.representative')]) }}"
                                >
                                <span class="error">{{ $errors->first('representative') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12">{{ __('custom.registered_at') }}:</label>
                        <div class="row w-70">
                            <div class="col-lg-12">
                                <input type="text" class="input-box" name="created_at" value="{{ $orgData->created_at }}" disabled>
                                <span class="error">{{ $errors->first('created_at') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12">{{ __('custom.phone_number') }}:</label>
                        <div class="row w-70">
                            <div class="col-lg-12">
                                <input
                                    type="text"
                                    class="input-box"
                                    name="phone"
                                    value="{{ old('phone', $orgData->phone) }}"
                                    maxlength="40"
                                    required
                                    title="{{ __('custom.required_mgs', ['field' => ultrans('custom.phone_number')]) }}"
                                >
                                <span class="error">{{ $errors->first('phone') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12">{{ __('custom.email') }}:</label>
                        <div class="row w-70">
                            <div class="col-lg-12">
                                <input
                                    type="text"
                                    class="input-box"
                                    name="email"
                                    value="{{ old('email', $orgData->email) }}"
                                    maxlength="255"
                                    required
                                    title="{{ __('custom.required_mgs', ['field' => ultrans('custom.email')]) }}"
                                >
                                <span class="error">{{ $errors->first('email') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12">{{ __('custom.in_av') }}:</label>
                        <div class="col-sm-4 p-l-none">
                            @include('components.checkbox', ['name' => 'in_av', 'checked' => old('in_av', $orgData->in_av)])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12">{{ __('custom.candidate') }}:</label>
                        <div class="col-sm-4 p-l-none">
                            @include('components.checkbox', ['name' => 'is_candidate', 'checked' => old('is_candidate', $orgData->is_candidate)])
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="col-md-10">
                    <div class="form-group row">
                        <label class="col-lg-4 col-xs-12">{{ __('custom.experience_info') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            <div class="nano txt-area-height">
                                <textarea
                                    class="txt-area no-outline p-a-5 nano-content"
                                    name="description"
                                    rows="5"
                                    cols="40"
                                    maxlength="8000"
                                >{{ old('description', $orgData->description) }}</textarea>
                            </div>
                            <span class="error">{{ $errors->first('description') }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12">{{ __('custom.reference_materials') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            <div class="nano txt-area-height">
                                <textarea
                                    class="txt-area no-outline p-a-5 nano-content"
                                    name="references"
                                    rows="5"
                                    cols="40"
                                    maxlength="8000"
                                >{{ old('references', $orgData->references) }}</textarea>
                            </div>
                            <span class="error">{{ $errors->first('references') }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-1">{{ __('custom.status') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            <div class="headerDropdown">
                                <select name="status" class="ams-dropdown custom-select w-100">
                                    @if (isset($statuses))
                                        @foreach ($statuses as $statusIndex => $status)
                                            <option
                                                value="{{ $statusIndex }}"
                                                @if (old('status'))
                                                    {{ old('status') == $statusIndex ? 'selected' : ''}}
                                                @else
                                                    {{ $orgData->status == $statusIndex ? 'selected' : ''}}
                                                @endif
                                            >{{ $status }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <i class="caret"></i>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-1">{{ __('custom.last_updated_by') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            {{ isset($orgData->updated_by_username) ? $orgData->updated_by_username : '' }}
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-1">{{ __('custom.last_updated_at') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            {{ isset($orgData->updated_at) ? date('Y-m-d H:i:s', strtotime($orgData->updated_at)) : ''}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12 text-center">
                @include('components.button', ['buttonLabel' => __('custom.save'), 'name' => 'edit'])
            </div>
        </div>
    </form>

    <hr class="hr-thin">

    <!-- ------------MESSAGES ------------------->
    <div class="row msg-m-b">
        <div class="col-lg-6 p-l-40"><h2>{{ __('custom.messages') }}</h2></div>
        <div class="col-lg-6 text-right p-r-50">
        @if (empty($messages))
            <a
                href="{{ route('admin.messages.add', ['id' => !empty($orgData) ? $orgData->id : null]) }}"
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
                            <tr class="{{ !$message->read ? 'message-not-read' : ''}}">
                                <td>
                                    <img src="{{ asset('img/arrow.svg') }}"
                                        height="30px" width="30px" class="p-r-5 {{ $message->recipient_org_id != null ? 'rotate-180' : ''}}"/>
                                </td>
                                <td>
                                    <img src="{{ !$message->read ? asset('img/circle-fill.svg') : asset('img/circle-no-fill.svg') }}"
                                        height="30px" width="30px" class="p-r-5"/>
                                    {{ $message->subject }}
                                </td>
                                <td class="text-center">{{ $message->created_at }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.messages', [
                                            'id' => $message->parent_id  ? $message->parent_id : $message->id,
                                            'orgId' => $orgData->id
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
                href="{{ route('admin.messages.add', ['id' => !empty($orgData) ? $orgData->id : null]) }}"
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
                    <a href="{{ route('admin.fileDowload', $file->id) }}">
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
