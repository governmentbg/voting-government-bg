@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')

<div class="row m-r-none m-l-none">
    @include('components.status')
    <div class="col-lg-12 m-t-30">
        <form method="get" action="{{ url('/admin/actionsHistory') }}">
            @if (request()->has('sort') && request()->has('order'))
                <input type="hidden" name="sort" value="{{ request()->get('sort')}}">
                <input type="hidden" name="order" value="{{ request()->get('order')}}">
            @endif
            <div class="row">
                <div class="col-lg-3">
                    <div class="row form-group">
                        <label for="module" class="col-form-label col-lg-3">{{ __('custom.module') }}:</label>
                        <div class="headerDropdown col-lg-8">
                            <select name="module" class="ams-dropdown custom-select js-drop-filter p-t-3">
                                <option value="all">{{ __('custom.all') }}</option>
                                @if (isset($modules))
                                    @foreach ($modules as $moduleIndex => $moduleName)
                                        <option
                                            value="{{ $moduleIndex }}"
                                            {{ isset($filters['module']) && $filters['module'] == $moduleIndex ? 'selected' : '' }}
                                        >{{ $moduleName }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <i class="caret"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="row form-group">
                        <label for="username" class="col-form-label col-lg-3">{{ __('custom.eik_name') }}:</label>
                        <div class="col-lg-8">
                            <input
                                type="text"
                                name="username"
                                placeholder="{{ __('custom.search') }}"
                                value="{{ isset($filters['username']) && $filters['username'] ? $filters['username']: '' }}"
                                class="form-control js-search search-box no-outline js-focusout-submit"
                            >
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="form-group row">
                        <label for="action" class="col-form-label col-lg-3" style="min-width: 85px">{{ __('custom.actions') }}:</label>
                        <div class="headerDropdown col-lg-8">
                            <select name="action" class="ams-dropdown custom-select js-drop-filter p-t-3">
                                <option value="all">{{ ultrans('custom.all') }}</option>
                                @if (isset($actions))
                                    @foreach ($actions as $actionIndex => $actionName)
                                        <option
                                            value="{{ $actionIndex }}"
                                            {{ isset($filters['action']) && $filters['action'] == $actionIndex ? 'selected' : '' }}
                                        >{{ $actionName }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <i class="caret"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="from-group row">
                <div class="col-lg-3">
                    <div class="form-group row">
                        <label for="module" class="col-form-label col-lg-3">{{ __('custom.tour') }}:</label>
                        <div class="headerDropdown col-lg-8">
                            <select name="voting_tour_id" class="ams-dropdown custom-select js-drop-filter p-t-3">
                                <option value="">{{ __('custom.no_tour') }}</option>
                                @if (!empty($toursList))
                                    @foreach ($toursList as $index => $tourData)
                                        <option
                                            value="{{ $tourData->id }}"
                                            {{ isset($filters['tour_id']) && $filters['tour_id'] == $tourData->id ? 'selected' : '' }}
                                        >{{ $tourData->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            <i class="caret"></i>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="form-group row">
                        <label for="ip_address" class="col-form-label col-lg-3">{{ __('custom.ip_address') }}:</label>
                        <div class="col-lg-8">
                            <input
                                type="text"
                                name="ip_address"
                                placeholder="{{__('custom.search')}}"
                                value="{{ isset($filters['ip_address']) && $filters['ip_address'] ? $filters['ip_address'] : '' }}"
                                class="form-control js-search search-box float-right no-outline js-focusout-submit"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="from-group row m-b-30">
                <div class="offset-lg-3 col-lg-5">
                    <div class="form-group row p-l-none">
                        <label for="period_from" class="col-lg-3 col-form-label">{{ __('custom.period_from_to') }}:</label>
                        <div class="col-lg-8 display-inherit">
                            <!-- From -->
                            @include('components.datepicker', [
                                'name' => 'period_from',
                                'value' => isset($filters['period_from']) && $filters['period_from'] ? $filters['period_from']: ''
                            ])
                            <img src="{{ asset('img/calendar.svg') }}" height="30px" width="30px" id="period_from" class="m-r-10 p-t-3 m-l-p5 c-pointer"/>
                            <!-- To -->
                            @include('components.datepicker', [
                                'name' => 'period_to',
                                'value' => isset($filters['period_to']) && $filters['period_to'] ? $filters['period_to']: ''
                            ])
                            <img src="{{ asset('img/calendar.svg') }}" height="30px" width="30px" id="period_to" class="p-t-3 m-l-p5 c-pointer"/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="table-wrapper m-t-30">
            <div class="table-responsive">
                <table class="table table-striped ams-table voting-tours-list" data-toggle="table">
                    <thead>
                        <tr>
                            <th class="w-20">
                                <a
                                    class="c-white {{ app('request')->sort == 'occurrence' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\ActionsHistoryController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['sort' => 'occurrence', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc']
                                            )
                                        )
                                    }}"
                                >{{ __('custom.occurrence') }}<img src="{{ app('request')->sort == 'occurrence' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/>
                                </a>
                            </th>
                            <th class="w-20">
                                <a
                                    class="c-white {{ app('request')->sort == 'username' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\ActionsHistoryController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['sort' => 'username', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc']
                                            )
                                        )
                                    }}"
                                >{{ __('custom.eik_name') }}<img src="{{ app('request')->sort == 'username' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/></a>
                            </th>
                            <th class="w-20">
                                {{ __('custom.actions') }}
                            </th>
                            <th class="w-15">
                                {{ __('custom.module') }}
                            </th>
                            <th class="w-10">
                                <a
                                    class="c-white {{ app('request')->sort == 'object' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\ActionsHistoryController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['sort' => 'object', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc']
                                            )
                                        )
                                    }}"
                                >{{ __('custom.object') }}<img src="{{ app('request')->sort == 'object' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/></a>
                            </th>
                            <th class="w-15">
                                <a
                                    class="c-white {{ app('request')->sort == 'ip_address' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\ActionsHistoryController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['sort' => 'ip_address', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc']
                                            )
                                        )
                                    }}"
                                >{{ __('custom.ip_address') }}<img src="{{ app('request')->sort == 'ip_address' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/></a>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="text-left">
                        @if (!empty($historyList))
                            @foreach ($historyList as $singleRecord)
                                <tr>
                                    <td class="text-center">{{ $singleRecord->occurrence }}</td>
                                    <td class="text-left eik">{{ $singleRecord->user_id_username }}</td>
                                    <td class="text-center">
                                        @foreach ($actions as $id => $name)
                                            @if ($singleRecord->action == $id)
                                                {{ $name }}
                                            @endif
                                        @endforeach
                                   </td>
                                    <td>
                                        @foreach ($modules as $id => $name)
                                            @if ($singleRecord->module == $id)
                                                {{ $name }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        @if ($singleRecord->action != \App\ActionsHistory::TYPE_VOTED)
                                            {{ $singleRecord->object == null ? '' : $singleRecord->object }}
                                        @endif
                                    </td>
                                    <td>{{ $singleRecord->ip_address }}</td>
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
                @if (!empty($historyList))
                    {{
                        $historyList->appends([
                            'sort'           => app('request')->sort,
                            'order'          => app('request')->order,
                            'module'         => app('request')->module,
                            'username'       => app('request')->username,
                            'action'         => app('request')->action,
                            'ip_address'     => app('request')->ip_address,
                            'period_from'    => app('request')->period_from,
                            'period_to'      => app('request')->period_to,
                            'voting_tour_id' => app('request')->voting_tour_id,
                        ])->links()
                    }}
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
