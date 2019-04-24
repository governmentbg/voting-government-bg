@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')

@include('components.status')
<div class="row">
    <div class="col-lg-12 p-l-40 m-b-30"><h3>{{ __('custom.data_for') }} {{$org_data->name}}</h3></div>
</div>
<form method="POST" action="{{ url('/admin/organisations/update/'. $org_data->id)}}">
    <div class="row m-l-5">
        <div class="col-lg-6">
            <div class="col-md-10">
                {{ csrf_field() }}
                @include('components.errors')
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12"> {{ __('custom.org_name') }}:</label>
                    <div class="row w-50">
                        <div class="col-lg-12">
                            <input type="text" class="input-box" name="name" value="{{$org_data->name}}" required>
                            <span class="error">{{ $errors->first('name') }}</span>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12"> {{ __('custom.eik_bulstat') }}:</label>
                    <div class="row w-50">
                        <div class="col-lg-12">
                            <input type="text" class="input-box" name="eik" value="{{$org_data->eik}}" disabled>
                            <span class="error">{{ $errors->first('eik') }}</span>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12"> {{ __('custom.management_address') }}:</label>
                    <div class="row w-50">
                        <div class="col-lg-12">
                            <input type="text" class="input-box" name="address" value="{{$org_data->address}}" required>
                            <span class="error">{{ $errors->first('address') }}</span>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12"> {{ __('custom.representative') }}:</label>
                    <div class="row w-50">
                        <div class="col-lg-12">
                            <input type="text" class="input-box" name="representative" value="{{$org_data->representative}}" required>
                            <span class="error">{{ $errors->first('representative') }}</span>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12"> {{ __('custom.registered_at') }}:</label>
                    <div class="row w-50">
                        <div class="col-lg-12">
                            <input type="text" class="input-box" name="created_at" value="{{$org_data->created_at}}" disabled>
                            <span class="error">{{ $errors->first('created_at') }}</span>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12"> {{ __('custom.phone_number') }}:</label>
                    <div class="row w-50">
                        <div class="col-lg-12">
                            <input type="text" class="input-box" name="phone" value="{{$org_data->phone}}" required>
                            <span class="error">{{ $errors->first('phone') }}</span>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12"> {{ __('custom.email') }}:</label>
                    <div class="row w-50">
                        <div class="col-lg-12">
                            <input type="text" class="input-box" name="email" value="{{$org_data->email}}" required>
                            <span class="error">{{ $errors->first('email') }}</span>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12"> {{ __('custom.in_av') }}:</label>
                    <div class="col-sm-4 p-l-none">
                        @include('components.checkbox', ['name' => 'in_av', 'checked' => $org_data->in_av])
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12"> {{ __('custom.candidate') }}:</label>
                    <div class="col-sm-4 p-l-none">
                        @include('components.checkbox', ['name' => 'is_candidate', 'checked' => $org_data->is_candidate])
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="col-md-10">
                <div class="form-group row">
                    <label class="col-lg-4 col-xs-12">{{ __('custom.experience_info') }}:</label>
                    <div class="col-sm-8 col-xs-6 p-r-none">
                        <textarea
                            class="txt-area no-outline p-a-5"
                            name="description"
                            rows="3"
                            cols="40"
                        >{{$org_data->description}}</textarea>
                        <span class="error">{{ $errors->first('description') }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-12">{{ __('custom.reference_materials') }}:</label>
                    <div class="col-sm-8 col-xs-6 p-r-none">
                        <textarea
                            class="txt-area no-outline p-a-5"
                            name="references"
                            rows="3"
                            cols="40"
                        >{{$org_data->references}}</textarea>
                        <span class="error">{{ $errors->first('references') }}</span>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-1">{{ __('custom.status') }}:</label>
                    <div class="col-sm-8 col-xs-6 p-r-none">
                        <select name="status" class="ams-dropdown custom-select w-100">
                            @if (isset($candidateStatuses))
                                @foreach($candidateStatuses as $candidateIndex => $candidateStatuses)
                                    <option value="{{$candidateIndex}}" {{$org_data->status == $candidateIndex ? 'selected' : ''}}>{{$candidateStatuses}}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-1">{{ __('custom.last_updated_by') }}:</label>
                    <div class="col-sm-8 col-xs-6 p-r-none">
                        {{isset($org_data->updated_by_username) ? $org_data->updated_by_username : ''}}
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-4 col-xs-1">{{ __('custom.last_updated_at') }}:</label>
                    <div class="col-sm-8 col-xs-6 p-r-none">
                        {{$org_data->updated_at}}
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

@if($messages->isNotEmpty())
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
                            <th></th>
                            <th class="w-50">{{ __('custom.title') }}</th>
                            <th class="w-30">{{ __('custom.date') }}</th>
                            <th class="w-20">{{ __('custom.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $message)
                        <tr class="{{ !$message->read ? 'message-not-read' : ''}}">
                            <td>
                                <img 
                                    src="{{ asset('img/arrow.svg') }}" 
                                    height="30px" 
                                    width="30px" 
                                    class="p-r-5 {{ $message->recipient_org_id != null ? 'rotate-180' : ''}}"
                                    />
                            </td>
                            <td>
                                <img src="{{ !$message->read ? asset('img/circle-fill.svg') : asset('img/circle-no-fill.svg') }}" height="30px" width="30px" class="p-r-5"/>
                                {{ $message->subject }}
                            </td>
                            <td class="text-center">{{ date('Y-m-d', strtotime($message->created_at)) }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.messages', ['id' => $message->parent_id  ? $message->parent_id : $message->id])}}">
                                    <img src="{{ asset('img/view.svg') }}" height="30px" width="30px" class="p-r-5"/>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif
@if (!empty($files))
<hr class="hr-thin">
    <div class="row">
        <div class="col-lg-12 p-l-40"><h2>{{ __('custom.applied_files') }}</h2></div>
    </div>

    @foreach ($files as $singleFile)
        <div class="col-lg-6">
            <label class="col-md-6 col-xs-12">{{$singleFile->name}}</label>
            <div class="col-md-6 display-inline">
                <a href="{{route('admin.fileDowload', $singleFile->id)}}"><img src="{{ asset('img/download.svg') }}" height="30px" width="30px" class="p-r-5"/></a>
            </div>
        </div>
    @endforeach
@endif
@endsection
