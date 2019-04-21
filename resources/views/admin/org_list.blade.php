@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')

<div class="row">
    @include('components.status')
    <div class="col-lg-10">
        <div>
            <img src="{{ asset('img/tick.svg') }}" height="30px" width="30px" class="display-inline m-b-8 p-r-5"/>
            <h3 class="display-inline">{{$tourData->name}}</h3>
        </div>
        <form method="get" action="{{ url('/admin/organisations') }}">
            <div class="row">
                <div class="col-lg-4 display-inline">
                    <label for="status" class="text-left">{{__('custom.status')}}:</label>
                    <select name="status" class="ams-dropdown custom-select w-50 js-drop-filter">
                        <option value="all">{{__('custom.all')}}</option>
                        @if (isset($statuses))
                            @foreach($statuses as $statIndex => $statusName)
                                <option value="{{$statIndex}}" {{isset($filters['statuses'][0]) && $filters['statuses'][0] == $statIndex ? 'selected' : ''}}>{{$statusName}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-lg-5 display-inline">
                    <div class="display-inline ">
                        <label for="eik">{{__('custom.eik')}}:</label>
                    </div>
                    <input type="text" name="eik" placeholder="{{__('custom.search')}}" value="{{isset($filters['eik']) && $filters['eik'] ? $filters['eik']: ''}}" class="js-search search-box float-right w-70 js-focusout-submit">
                </div>
                <div class="col-lg-3 display-inline">
                    <label for="is_candidate">{{__('custom.candidate')}}:</label>
                    <select name="is_candidate" class="ams-dropdown custom-select w-50 js-drop-filter">
                        <option value="all">Виж всички</option>
                        @if (isset($candidateStatuses))
                            @foreach($candidateStatuses as $candidateIndex => $candidateStatuses)
                                <option value="{{$candidateIndex}}" {{isset($filters['is_candidate']) && $filters['is_candidate'] == $candidateIndex ? 'selected' : ''}}>{{$candidateStatuses}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
            <div class="row m-t-10">
                <div class="offset-lg-4 col-lg-5 display-inline">
                    <div class="display-inline">
                        <label for="email">{{__('custom.email')}}:</label>
                    </div>
                    <input type="text" name="email" placeholder="{{__('custom.search')}}" value="{{isset($filters['email']) && $filters['email'] ? $filters['email']: ''}}" class="js-search search-box float-right w-70 js-focusout-submit">
                </div>
            </div>
            <div class="row m-t-10">
                <div class="offset-lg-4 col-lg-5 display-inline">
                    <div class="display-inline">
                        <label for="name">{{__('custom.org_name')}}:</label>
                    </div>
                    <input type="text" name="name" placeholder="{{__('custom.search')}}" value="{{isset($filters['name']) && $filters['name'] ? $filters['name']: ''}}" class="js-search search-box float-right w-70 js-focusout-submit">
                </div>
            </div>
            <div class="row m-t-10">
                <div class="offset-lg-4 col-lg-6 display-inline">
                    <div class="display-inline">
                        <label for="registered_period">{{__('custom.registered_period')}}:</label>
                    </div>
                    <div class="display-inline float-right col-lg-9 p-l-none">
                        <!-- From -->
                        @include('components.datepicker', ['name' => 'reg_date_from', 'value' => isset($filters['reg_date_from']) && $filters['reg_date_from'] ? $filters['reg_date_from']: ''])
                        <img src="{{ asset('img/calendar.svg') }}" height="30px" width="30px" class="m-r-10"/>
                        <!-- To -->
                        @include('components.datepicker', ['name' => 'reg_date_to', 'value' => isset($filters['reg_date_to']) && $filters['reg_date_to'] ? $filters['reg_date_to']: ''])
                        <img src="{{ asset('img/calendar.svg') }}" height="30px" width="30px"/>
                    </div>
                </div>
            </div>
        </form>
        <div class="table-wrapper m-t-20">
            <div class="table-responsive">
                <table class="table table-striped ams-table voting-tours-list" data-toggle="table">
                    <thead>
                        <tr>
                            <th class="w-20" data-field="name" data-sortable="true">{{ __('custom.organisation') }}</th>
                            <th class="w-10" data-field="eik" data-sortable="true">{{ __('custom.eik') }}</th>
                            <th class="w-10" data-field="status" data-sortable="true">{{ __('custom.status') }}</th>
                            <th class="w-10" data-field="is_candidate" data-sortable="true">{{ __('custom.candidate') }}</th>
                            <th class="w-25" data-field="registered_at" data-sortable="true">{{ __('custom.registered_at') }}</th>
                            <th class="w-15" data-field="email" data-sortable="true">{{ __('custom.email') }}</th>
                            <th class="w-10">{{ __('custom.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @if (!empty($organisationList))
                            @foreach ($organisationList as $singleOrg)
                                <tr>
                                    <td>{{$singleOrg->name}}</td>
                                    <td class="text-left eik">{{$singleOrg->eik}}</td>
                                    <td>{{$statuses[$singleOrg->status]}}</td>
                                    <td>
                                        @if ($singleOrg->is_candidate)
                                            @include('components.checkbox', ['checked' => true, 'readonly' => true, 'name' => 'holder'])
                                        @endif
                                    </td>
                                    <td>{{$singleOrg->created_at}}</td>
                                    <td>{{$singleOrg->email}}</td>
                                    <td>
                                        <!-- <a href="{{ url('admin/organisations/view/' . $singleOrg->id) }}"><img src="{{ asset('img/view.svg') }}" height="30px" width="30px"/></a> -->
                                        <a href="{{ url('admin/organisations/edit/' . $singleOrg->id) }}"><img src="{{ asset('img/edit.svg') }}" height="30px" width="30px"/></a>
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
