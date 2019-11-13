@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')

<div class="row m-r-none m-l-none">
    @include('components.status')
    <div class="col-lg-12 m-t-30">
        <form method="get" action="{{ url('/admin/organisations') }}" id="orgList">
            @if(request()->has('sort') && request()->has('order'))
                <input type="hidden" name="sort" value="{{ request()->get('sort')}}">
                <input type="hidden" name="order" value="{{ request()->get('order')}}">
            @endif

            <div class="row">
                <div class="col-lg-3">
                    <div class="row form-group">
                        <label for="status" class="col-form-label col-lg-3">{{ __('custom.status') }}:</label>
                        <div class="headerDropdown col-lg-8">
                            <select name="status" class="ams-dropdown custom-select js-drop-filter p-t-3">
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
                            <i class="caret"></i>
                        </div>
                    </div>
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
                        <label for="is_candidate" class="col-form-label col-lg-3" style="min-width: 85px">{{ __('custom.candidate') }}:</label>
                        <div class="headerDropdown col-lg-8">
                            <select name="is_candidate" class="ams-dropdown custom-select js-drop-filter p-t-3">
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
                            <i class="caret"></i>
                        </div>
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
                            <img src="{{ asset('img/calendar.svg') }}" height="30px" width="30px" id="reg_date_from" class="m-r-10 p-t-3 m-l-p5 c-pointer"/>
                            <!-- To -->
                            @include('components.datepicker', [
                                'name' => 'reg_date_to',
                                'value' => isset($filters['reg_date_to']) && $filters['reg_date_to'] ? $filters['reg_date_to']: ''
                            ])
                            <img src="{{ asset('img/calendar.svg') }}" height="30px" width="30px" id="reg_date_to" class="p-t-3 m-l-p5 c-pointer"/>
                        </div>
                    </div>
                </div>
            </div>
            @if (!empty($organisationList))
                <div class="col-lg-12 text-right p-r-none p-b-none">
                    <button
                        class="btn btn-primary add"
                        type="submit"
                        name="download"
                    >{{ uctrans('custom.download') }}</button>
                </div>
            @endif
        </form>
        <div class="table-wrapper {{!empty($organisationList) ? 'm-t-30' : 'm-t-90' }}">
            <div class="table-responsive">
                <table class="table table-striped ams-table voting-tours-list" data-toggle="table">
                    <thead>
                        <tr>
                            <th class="w-30">
                                <a
                                    class="c-white {{ app('request')->sort == 'name' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\OrganisationController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['sort' => 'name', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc']
                                            )
                                        )
                                    }}"
                                >{{ __('custom.organisation') }}<img src="{{ app('request')->sort == 'name' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/>
                                </a>
                            </th>
                            <th class="w-10">
                                <a
                                    class="c-white {{ app('request')->sort == 'eik' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\OrganisationController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['sort' => 'eik', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc']
                                            )
                                        )
                                    }}"
                                >{{ __('custom.eik') }}<img src="{{ app('request')->sort == 'eik' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/></a>
                            </th>
                            <th class="w-15">
                                <a
                                    class="c-white {{ app('request')->sort == 'status' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\OrganisationController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['sort' => 'status', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc']
                                            )
                                        )
                                    }}"
                                >{{ __('custom.status') }}<img src="{{ app('request')->sort == 'status' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/></a>
                            </th>
                            <th class="w-10">
                                <a
                                    class="c-white {{ app('request')->sort == 'is_candidate' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\OrganisationController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['sort' => 'is_candidate', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc']
                                            )
                                        )
                                    }}"
                                >{{ __('custom.candidate') }}<img src="{{ app('request')->sort == 'is_candidate' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/></a>
                            </th>
                            <th class="w-10">
                                <a
                                    class="c-white {{ app('request')->sort == 'created_at' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\OrganisationController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['sort' => 'created_at', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc']
                                            )
                                        )
                                    }}"
                                >{{ __('custom.registered_at') }}<img src="{{ app('request')->sort == 'created_at' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/></a>
                            </th>
                            <th class="w-15">
                                <a
                                    class="c-white {{ app('request')->sort == 'email' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\OrganisationController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['sort' => 'email', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc']
                                            )
                                        )
                                    }}"
                                >{{ __('custom.email') }}<img src="{{ app('request')->sort == 'email' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/></a>
                            </th>
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
                        @else
                            <tr>
                                <td colspan="7" class="text-center">{{__('custom.no_info')}}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="display-flex justify-center">
                @if (!empty($organisationList))
                    {{
                        $organisationList->appends([
                            'sort'          => app('request')->sort,
                            'order'         => app('request')->order,
                            'status'        => app('request')->status,
                            'eik'           => app('request')->eik,
                            'is_candidate'  => app('request')->is_candidate,
                            'email'         => app('request')->email,
                            'name'          => app('request')->name,
                            'reg_date_from' => app('request')->reg_date_from,
                            'reg_date_to'   => app('request')->reg_date_to,
                        ])->links()
                    }}
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
