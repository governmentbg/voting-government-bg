@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')

<div class="row m-r-none m-l-none">
    @include('components.status')
    <div class="col-lg-12 m-t-30">
        <form method="get" action="{{ url('/admin/organisations') }}">
            <div class="from-group row">
                <div class="col-lg-3">
                    <label for="status" class="col-form-label col-lg-2">{{ __('custom.status') }}:</label>
                    <select name="status" class="col-lg-8 ams-dropdown custom-select js-drop-filter p-t-3">
                        <option value="all">{{ __('custom.all') }}</option>
                        @if (isset($statuses))
                            @foreach ($statuses as $statIndex => $statusName)
                                <option
                                    value="{{ $statIndex }}"
                                    {{ isset($filters['statuses'][0]) && $filters['statuses'][0] == $statIndex ? 'selected' : '' }}
                                >{{ $statusName }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-lg-5">
                    <div class="row form-group">
                        <label for="eik" class="col-form-label col-lg-3">{{ __('custom.eik') }}:</label>
                        <div class="col-lg-8">
                            <input
                                type="text"
                                name="eik"
                                placeholder="{{__('custom.search')}}"
                                value="{{isset($filters['eik']) && $filters['eik'] ? $filters['eik']: ''}}"
                                class="form-control js-search search-box no-outline js-focusout-submit"
                            >
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group row">
                        <label for="is_candidate" class="col-form-label col-lg-3">{{ __('custom.candidate') }}:</label>
                        <select name="is_candidate" class="col-lg-8 form-control ams-dropdown custom-select js-drop-filter p-t-3">
                            <option value="all">{{ __('custom.all') }}</option>
                            @if (isset($candidateStatuses))
                                @foreach ($candidateStatuses as $candidateIndex => $candidateStatuses)
                                    <option
                                        value="{{$candidateIndex}}"
                                        {{ isset($filters['is_candidate']) && $filters['is_candidate'] == $candidateIndex ? 'selected' : '' }}
                                    >{{ $candidateStatuses }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>
            <div class="from-group row">
                <div class="offset-lg-3 col-lg-5">
                    <div class="form-group row">
                        <label for="email" class="col-form-label col-lg-3">{{ __('custom.email') }}:</label>
                        <div class="col-lg-8">
                            <input
                                type="text"
                                name="email"
                                placeholder="{{__('custom.search')}}"
                                value="{{ isset($filters['email']) && $filters['email'] ? $filters['email']: '' }}"
                                class="form-control js-search search-box float-right no-outline js-focusout-submit"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="from-group row">
                <div class="offset-lg-3 col-lg-5">
                    <div class="row form-group">
                        <label for="name" class="col-lg-3 col-form-label">{{ __('custom.org_name') }}:</label>
                        <div class="col-lg-8">
                            <input
                                type="text"
                                name="name"
                                placeholder="{{__('custom.search')}}"
                                value="{{ isset($filters['name']) && $filters['name'] ? $filters['name']: '' }}"
                                class="form-control js-search search-box no-outline js-focusout-submit"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="from-group row m-b-30">
                <div class="offset-lg-3 col-lg-5">
                    <div class="form-group row p-l-none">
                        <label for="registered_period" class="col-lg-3 col-form-label">{{ __('custom.registered_period') }}:</label>
                        <div class="col-lg-8 display-inherit">
                            <!-- From -->
                            @include('components.datepicker', [
                                'name' => 'reg_date_from',
                                'value' => isset($filters['reg_date_from']) && $filters['reg_date_from'] ? $filters['reg_date_from']: ''
                            ])
                            <img src="{{ asset('img/calendar.svg') }}" height="30px" width="30px" id="reg_date_from" class="m-r-10 p-t-3 m-l-p5"/>
                            <!-- To -->
                            @include('components.datepicker', [
                                'name' => 'reg_date_to',
                                'value' => isset($filters['reg_date_to']) && $filters['reg_date_to'] ? $filters['reg_date_to']: ''
                            ])
                            <img src="{{ asset('img/calendar.svg') }}" height="30px" width="30px" id="reg_date_to" class="p-t-3 m-l-p5"/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="table-wrapper m-t-60">
            <div class="table-responsive">
                <table class="table table-striped ams-table voting-tours-list" data-toggle="table">
                    <thead>
                        <tr>
                            <th class="w-30" data-field="name" data-sortable="true">{{ __('custom.organisation') }}</th>
                            <th class="w-10" data-field="eik" data-sortable="true">{{ __('custom.eik') }}</th>
                            <th class="w-15" data-field="status" data-sortable="true">{{ __('custom.status') }}</th>
                            <th class="w-10" data-field="is_candidate" data-sortable="true">{{ __('custom.candidate') }}</th>
                            <th class="w-10" data-field="registered_at" data-sortable="true">{{ __('custom.registered_at') }}</th>
                            <th class="w-15" data-field="email" data-sortable="true">{{ __('custom.email') }}</th>
                            <th class="w-10">{{ __('custom.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-left">
                        @if (!empty($organisationList))
                            @foreach ($organisationList as $singleOrg)
                                <tr>
                                    <td>{{ $singleOrg->name }}</td>
                                    <td class="text-left eik">{{ $singleOrg->eik }}</td>
                                    <td>{{ isset($statuses[$singleOrg->status]) ? $statuses[$singleOrg->status] : '' }}</td>
                                    <td class="text-center">
                                        @if ($singleOrg->is_candidate)
                                            <img src="{{ asset('img/checked.png') }}" height="26px" width="30px"/>
                                        @endif
                                    </td>
                                    <td>{{ $singleOrg->created_at }}</td>
                                    <td>{{ $singleOrg->email }}</td>
                                    <td class="text-center">
                                        <a
                                            href="{{ route('admin.org_edit', ['id' => $singleOrg->id]) }}"
                                            title="{{ __('custom.edit') }}"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                        >
                                        <img src="{{ asset('img/edit.svg') }}" height="30px" width="30px"/>
                                    </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="display-flex justify-center">
                @if (!empty($organisationList))
                    {{ $organisationList->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
