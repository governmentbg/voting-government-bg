@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')

<div class="row m-r-none m-l-none">
    <div class="col-lg-12">
        @include('components.status')
        @include('components.errors')
        <form method="get" action="{{ url('/admin/messages') }}">
            <input type="hidden" name="sort" value="{{ request()->get('sort')}}">
            <input type="hidden" name="order" value="{{ request()->get('order')}}">

            <div class="from-group row">
                <div class="col-lg-3">
                    <div class="row form-group">
                        <label for="filters[status]" class="col-form-label col-lg-3">{{__('custom.status')}}:</label>
                        <div class="headerDropdown col-lg-8">
                            <select name="filters[status]" class="ams-dropdown custom-select js-drop-filter p-t-3">
                                @if (isset($statuses))
                                    @foreach ($statuses as $statIndex => $statusName)
                                        <option
                                            value="{{ $statIndex }}"
                                            {{ isset($filters['status']) && $filters['status'] == $statIndex ? 'selected' : '' }}
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
                        <label for="filters[subject]" class="col-form-label col-lg-3">{{ __('custom.subject') }}:</label>
                        <div class="col-lg-8">
                            <input
                                type="text"
                                name="filters[subject]"
                                placeholder="{{ __('custom.search') }}"
                                value="{{ isset($filters['subject']) && $filters['subject'] ? $filters['subject']: '' }}"
                                maxlength="255"
                                class="form-control js-search search-box no-outline js-focusout-submit"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="from-group row">
                <div class="offset-lg-3 col-lg-5">
                    <div class="row form-group">
                        <label for="filters[org_name]"  class="col-form-label col-lg-3">{{ __('custom.send_from') }}:</label>
                        <div class="col-lg-8">
                            <input
                                type="text"
                                name="filters[org_name]"
                                placeholder="{{ __('custom.search') }}"
                                value="{{ isset($filters['org_name']) && $filters['org_name'] ? $filters['org_name']: '' }}"
                                maxlength="255"
                                class="form-control js-search search-box no-outline js-focusout-submit"
                            >
                        </div>
                    </div>
                </div>
            </div>
            <div class="from-group row m-b-30">
                <div class="offset-lg-3 col-lg-5">
                    <div class="form-group row p-l-none">
                        <label for="registered_period" class="col-lg-3 col-form-label">{{ __('custom.date_period') }}:</label>
                        <div class="col-lg-8 display-inherit">
                            <!-- From -->
                            @include('components.datepicker', [
                                'name' => 'filters[date_from]',
                                'value' => isset($filters['date_from']) && $filters['date_from'] ? $filters['date_from']: ''
                            ])
                            <img src="{{ asset('img/calendar.svg') }}" height="30px" width="30px" id="reg_date_from" class="m-r-10 p-t-3 m-l-p5 c-pointer"/>
                            <!-- To -->
                            @include('components.datepicker', [
                                'name' => 'filters[date_to]',
                                'value' => isset($filters['date_to']) && $filters['date_to'] ? $filters['date_to']: ''
                            ])
                            <img src="{{ asset('img/calendar.svg') }}" height="30px" id="reg_date_to" width="30px" class="p-t-3 m-l-p5 c-pointer"/>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="table-wrapper m-t-60">
            <div class="table-responsive">
                <table class="table table-striped ams-table messages-list" data-toggle="table">
                    <thead>
                        <tr>
                            <th class="w-20">
                                <a
                                    class="c-white {{ app('request')->orderBy == 'subject' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\MessagesController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['orderBy' => 'subject', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc']
                                            )
                                        )
                                    }}"
                                >{{ __('custom.subject') }}<img src="{{ app('request')->orderBy == 'subject' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/></a>
                            </th>
                            <th class="w-10">{{ __('custom.from') }}</th>
                            <th class="w-10">
                                <a
                                    class="c-white {{ app('request')->orderBy == 'created_by' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\MessagesController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['orderBy' => 'created_by', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc']
                                            )
                                        )
                                    }}"
                                >{{ __('custom.date') }}<img src="{{ app('request')->orderBy == 'created_by' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/></a>
                            </th>
                            <th class="w-10">{{ __('custom.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @if (!empty($messages))
                            @foreach ($messages as $message)
                                <tr class="{{ !$message->read ? 'message-not-read' : ''}}">
                                    <td class="text-left">
                                        <img src="{{ !$message->read ? asset('img/circle-fill.svg') : asset('img/circle-no-fill.svg') }}"
                                            height="30px" width="30px" class="p-r-5"/>
                                        {{ $message->subject }}
                                    </td>
                                    <td class="text-left">{{ $message->sender_org_name }}</td>
                                    <td>{{ $message->created_at }}</td>
                                    <td>
                                        <a href="{{ route('admin.messages', [
                                                'id' => $message->parent_id ? $message->parent_id : $message->id,
                                                'orgId' => null
                                            ]) . ($message->parent_id ? '#'. $message->id : '') }}"
                                            title="{{ __('custom.view') }}"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            >
                                            <img src="{{ asset('img/view.svg') }}" height="30px" width="30px" class="p-r-5"/>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4">{{__('custom.no_info')}}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="display-flex justify-center">
                @if (!empty($messages))
                    {{ $messages->appends(['orderBy' => app('request')->orderBy, 'order' => app('request')->order])->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
