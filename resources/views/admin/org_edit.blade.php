@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')

@include('components.status')
@if (!empty($orgData))
    <div class="row">
        <div class="col-lg-12 p-l-40 m-b-30"><h3>{{ __('custom.data_for') }} {{ $orgData->name }}</h3></div>
    </div>
    <form method="POST" enctype="multipart/form-data" action="{{ route('admin.org_update', ['id' => $orgData->id]) }}" class="edit-org">
        <div class="row m-l-5">
            <div class="col-lg-11 p-l-25 p-r-25">
                @include('components.errors')
            </div>
            <div class="col-lg-6">
                <div class="col-md-12">
                    {{ csrf_field() }}
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12">{{ __('custom.eik_bulstat') }}:</label>
                        <div class="row col-lg-8">
                            <div class="col-lg-12">
                                <input type="text" class="input-box" name="eik" value="{{ $orgData->eik }}" disabled>
                                <span class="error">{{ $errors->first('eik') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12">{{ __('custom.org_name') }}:</label>
                        <div class="row col-lg-8">
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
                        <div class="row col-lg-8">
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
                        <div class="row col-lg-8">
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
                        <div class="row col-lg-8">
                            <div class="col-lg-12">
                                <input type="text" class="input-box" name="created_at" value="{{ $orgData->created_at }}" disabled>
                                <span class="error">{{ $errors->first('created_at') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12">{{ __('custom.phone_number') }}:</label>
                        <div class="row col-lg-8">
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
                        <label class="col-sm-4 col-xs-12" title="{{ __('custom.email_hint') }}">{{ __('custom.email') }}:</label>
                        <div class="row col-lg-8">
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
                        <label class="col-lg-4" title="{{ __('custom.av_hint') }}">{{ __('custom.in_av') }}:</label>
                        <div class="col-lg-8">
                            @include('components.checkbox', ['name' => 'in_av', 'checked' => old('in_av', $orgData->in_av)])
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-12">{{ __('custom.candidate') }}:</label>
                        <div class="col-lg-8">
                            @include('components.checkbox', ['name' => 'is_candidate', 'checked' => old('is_candidate', $orgData->is_candidate)])
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="col-md-10">
                    <div class="form-group row">
                        <label class="col-lg-4 col-md-6 col-xs-6">{{ __('custom.experience_info') }}:</label>
                        <div class="col-lg-8 col-md-12 col-xs-12">
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
                        <label class="col-lg-4 col-md-6 col-xs-6">{{ __('custom.reference_materials') }}:</label>
                        <div class="col-lg-8 col-md-12 col-xs-12">
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
                        <label class="col-lg-4">{{ __('custom.status') }}:</label>
                        <div class="col-lg-8 headerDropdown">
                            <select name="status" class="ams-dropdown custom-select w-100 edit-status" data-old-status="{{ $orgData->status }}">
                                @if (isset($statuses))
                                    @foreach ($statuses as $statusIndex => $status)
                                        <option
                                            value="{{ $statusIndex }}"
                                            {{ in_array($statusIndex, $disabledStatuses) ? 'disabled' : '' }}
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
                    <div class="form-group row">
                        <label class="col-sm-4 col-xs-1">{{ __('custom.status_hint') }}:</label>
                        <div class="col-sm-8 col-xs-6 p-r-none">
                            {{ isset($orgData->status_hint) ? $orgData->status_hint : '' }}
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

    <div class="row mr-3 show-regs-wrapper mb-3">
        <div class="col-lg-12 text-right">
            @include('components.button', ['buttonLabel' => __('custom.show_reg_info'), 'name' => 'show', 'id' => 'showRegs'])
            <img src="{{ asset('img/cross.svg') }}" height="30px" width="30px" class="display-none cross-close">
        </div>
    </div>

    <div class="col-lg-12 display-none regs-tables">
        <div class="display-inline">
            <div class="col-lg-12 text-justify">
                <div class="col-lg-3 reg-bg inline-block v-align-top">
                    <h3 class="text-center">{{ __('custom.predefined_list_type_bul') }}</h3>
                    @if ($errors->has('pred_list_bul'))
                        <div class="form-group text-center">
                            <span class="error p-l-15">{{ $errors->first('pred_list_bul') }}</span>
                        </div>
                    @elseif (empty($orgDataPredBul))
                        <div class="form-group text-center">{{ __('custom.no_info') }}</div>
                    @else
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.org_name') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredBul->name) ? $orgDataPredBul->name : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.eik_bulstat') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredBul->eik) ? $orgDataPredBul->eik : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.reg_number') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredBul->reg_number) ? $orgDataPredBul->reg_number : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.city') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12" >{{ isset($orgDataPredBul->city) ? $orgDataPredBul->city : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.address') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12" >{{ isset($orgDataPredBul->address) ? $orgDataPredBul->address : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.representative') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12" >{{ isset($orgDataPredBul->representative) ? $orgDataPredBul->representative : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.registered_at') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredBul->reg_date) ? $orgDataPredBul->reg_date : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.email') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredBul->email) ? $orgDataPredBul->email : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.phone_number') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredBul->phone) ? $orgDataPredBul->phone : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-justified">
                            <label class="col-lg-2 col-xs-12">{{ __('custom.description') }}:</label>
                            <div class="col-lg-12 p-l-none">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredBul->description) ? $orgDataPredBul->description : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-justified">
                            <label class="col-lg-2 col-xs-12">{{ __('custom.goals') }}:</label>
                            <div class="col-lg-12 p-l-none">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredBul->goals) ? $orgDataPredBul->goals : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-justified">
                            <label class="col-lg-2 col-xs-12">{{ __('custom.tools') }}:</label>
                            <div class="col-lg-12 p-l-none">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredBul->tools) ? $orgDataPredBul->tools : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.status') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">
                                {{
                                    isset($orgDataPredBul->status_name)
                                        ? $orgDataPredBul->status_name
                                        : (isset($orgDataPredBul->status) ? $orgDataPredBul->status : '')
                                }}
                                </label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.status_date') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredBul->status_date) ? $orgDataPredBul->status_date : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.public_benefits') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">
                                {{ isset($orgDataPredBul->public_benefits)
                                    ? ($orgDataPredBul->public_benefits ? __('custom.public_benefits_yes') : __('custom.public_benefits_no'))
                                    : ''
                                }}
                                </label>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-lg-1 inline-block"></div>
                <div class="col-lg-3 reg-bg inline-block v-align-top">
                    <h3 class="text-center">{{ __('custom.predefined_list_type_tr') }}</h3>
                    @if ($errors->has('pred_list_trade'))
                        <div class="form-group text-center">
                            <span class="error p-l-15">{{ $errors->first('pred_list_trade') }}</span>
                        </div>
                    @elseif (empty($orgDataPredTrade))
                        <div class="form-group text-center">{{ __('custom.no_info') }}</div>
                    @else
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.org_name') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredTrade->name) ? $orgDataPredTrade->name : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.eik_bulstat') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredTrade->eik) ? $orgDataPredTrade->eik : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.reg_number') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredTrade->reg_number) ? $orgDataPredTrade->reg_number : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.city') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12" >{{ isset($orgDataPredTrade->city) ? $orgDataPredTrade->city : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.address') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12" >{{ isset($orgDataPredTrade->address) ? $orgDataPredTrade->address : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.representative') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12" >{{ isset($orgDataPredTrade->representative) ? $orgDataPredTrade->representative : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.registered_at') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredTrade->reg_date) ? $orgDataPredTrade->reg_date : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.email') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredTrade->email) ? $orgDataPredTrade->email : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.phone_number') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredTrade->phone) ? $orgDataPredTrade->phone : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-justified">
                            <label class="col-lg-2 col-xs-12">{{ __('custom.description') }}:</label>
                            <div class="col-lg-12 p-l-none">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredTrade->description) ? $orgDataPredTrade->description : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-justified">
                            <label class="col-lg-2 col-xs-12">{{ __('custom.goals') }}:</label>
                            <div class="col-lg-12 p-l-none">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredTrade->goals) ? $orgDataPredTrade->goals : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-justified">
                            <label class="col-lg-2 col-xs-12">{{ __('custom.tools') }}:</label>
                            <div class="col-lg-12 p-l-none">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredTrade->tools) ? $orgDataPredTrade->tools : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.status') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">
                                {{
                                    isset($orgDataPredTrade->status_name)
                                        ? $orgDataPredTrade->status_name
                                        : (isset($orgDataPredTrade->status) ? $orgDataPredTrade->status : '')
                                }}
                                </label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.status_date') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPredTrade->status_date) ? $orgDataPredTrade->status_date : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.public_benefits') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">
                                {{ isset($orgDataPredTrade->public_benefits)
                                    ? ($orgDataPredTrade->public_benefits ? __('custom.public_benefits_yes') : __('custom.public_benefits_no'))
                                    : ''
                                }}
                                </label>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-lg-1 inline-block"></div>
                <div class="col-lg-3 reg-bg inline-block v-align-top">
                    <h3 class="text-center">{{ __('custom.predefined_list_type') }}</h3>
                    @if ($errors->has('pred_list'))
                        <div class="form-group text-center">
                            <span class="error p-l-15">{{ $errors->first('pred_list') }}</span>
                        </div>
                    @elseif (empty($orgDataPred))
                        <div class="form-group text-center">{{ __('custom.no_info') }}</div>
                    @else
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.org_name') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPred->name) ? $orgDataPred->name : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.eik_bulstat') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPred->eik) ? $orgDataPred->eik : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.reg_number') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPred->reg_number) ? $orgDataPred->reg_number : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.city') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12" >{{ isset($orgDataPred->city) ? $orgDataPred->city : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.address') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12" >{{ isset($orgDataPred->address) ? $orgDataPred->address : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.representative') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12" >{{ isset($orgDataPred->representative) ? $orgDataPred->representative : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.registered_at') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPred->reg_date) ? $orgDataPred->reg_date : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.email') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPred->email) ? $orgDataPred->email : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.phone_number') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPred->phone) ? $orgDataPred->phone : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-justified">
                            <label class="col-lg-2 col-xs-12">{{ __('custom.description') }}:</label>
                            <div class="col-lg-12 p-l-none">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPred->description) ? $orgDataPred->description : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-justified">
                            <label class="col-lg-2 col-xs-12">{{ __('custom.goals') }}:</label>
                            <div class="col-lg-12 p-l-none">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPred->goals) ? $orgDataPred->goals : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-justified">
                            <label class="col-lg-2 col-xs-12">{{ __('custom.tools') }}:</label>
                            <div class="col-lg-12 p-l-none">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPred->tools) ? $orgDataPred->tools : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.status') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">
                                {{
                                    isset($orgDataPred->status_name)
                                        ? $orgDataPred->status_name
                                        : (isset($orgDataPred->status) ? $orgDataPred->status : '')
                                }}
                                </label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.status_date') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">{{ isset($orgDataPred->status_date) ? $orgDataPred->status_date : '' }}</label>
                            </div>
                        </div>
                        <div class="form-group row text-left">
                            <label class="col-lg-5 col-xs-12">{{ __('custom.public_benefits') }}:</label>
                            <div class="row col-lg-7">
                                <label class="col-sm-12 col-xs-12">
                                {{ isset($orgDataPred->public_benefits)
                                    ? ($orgDataPred->public_benefits ? __('custom.public_benefits_yes') : __('custom.public_benefits_no'))
                                    : ''
                                }}
                                </label>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <hr class="hr-thin">
    </div>

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

<div class="modal" tabindex="-1" role="dialog" id='confirmOrgDeclass'>
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('custom.declass_modal_title') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{ __('custom.declass_message') }}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('custom.cancel')}}</button>
                <button type="button" class="btn btn-primary confirm">{{ __('custom.confirm')}}</button>
            </div>
        </div>
    </div>
</div>
@php
    http2_push_image('/img/arrow.svg');
    http2_push_image('/img/circle-fill.svg');
    http2_push_image('/img/circle-no-fill.svg');
    http2_push_image('/img/view.svg');
    http2_push_image('/img/download.svg');
@endphp

@endsection
