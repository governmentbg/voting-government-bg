@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')

<div class="row m-t-30 m-r-none m-l-none">
    @include('components.status')
    @include('components.errors')
    <div class="col-lg-12">
        <form method="get" action="{{ url('/admin/messages') }}">
            <div class="from-group row">
                <div class="col-lg-3">
                    <label for="filters[status]" class="col-form-label col-lg-2">{{__('custom.status')}}:</label>
                    <select name="filters[status]" class="col-lg-8 ams-dropdown custom-select w-50 js-drop-filter p-t-3">
                        @if (isset($statuses))
                            @foreach($statuses as $statIndex => $statusName)
                                <option value="{{$statIndex}}" {{isset($filters['status']) && $filters['status'] == $statIndex ? 'selected' : ''}}>{{$statusName}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-lg-5">
                    <div class="row form-group">
                        <label for="filters[subject]" class="col-form-label col-lg-3">{{__('custom.subject')}}:</label>
                        <div class="col-lg-8">
                            <input
                                type="text"
                                name="filters[subject]"
                                placeholder="{{__('custom.search')}}"
                                value="{{isset($filters['subject']) && $filters['subject'] ? $filters['subject']: ''}}"
                                class="form-control js-search search-box float-right"
                            >
                        </div>
                    </div>
                    <input type="text" name="filters[subject]" placeholder="{{__('custom.search')}}" value="{{isset($filters['subject']) && $filters['subject'] ? $filters['subject']: ''}}" class="js-search search-box float-right w-70 js-focusout-submit">
                </div>
            </div>
            <div class="from-group row">
                <div class="offset-lg-3 col-lg-5">
                    <div class="row form-group">
                        <label for="filters[org_name]"  class="col-form-label col-lg-3">{{__('custom.send_from')}}:</label>
                        <div class="col-lg-8">
                            <input
                                type="text"
                                name="filters[org_name]"
                                placeholder="{{__('custom.search')}}"
                                value="{{isset($filters['org_name']) && $filters['org_name'] ? $filters['org_name']: ''}}"
                                class="form-control js-search search-box float-right"
                            >
                        </div>
                    </div>
                    <input type="text" name="filters[org_name]" placeholder="{{__('custom.search')}}" value="{{isset($filters['org_name']) && $filters['org_name'] ? $filters['org_name']: ''}}" class="js-search search-box float-right w-70 js-focusout-submit">
                </div>
            </div>
            <div class="from-group row m-b-30">
                <div class="offset-lg-3 col-lg-5">
                    <div class="form-group row p-l-none">
                        <label for="registered_period" class="col-lg-3 col-form-label">{{__('custom.date_period')}}:</label>

                        <div class="col-lg-8 display-inherit">
                            <!-- From -->
                            @include('components.datepicker', ['name' => 'filters[date_from]', 'value' => isset($filters['date_from']) && $filters['date_from'] ? $filters['date_from']: ''])
                            <img src="{{ asset('img/calendar.svg') }}" height="30px" width="30px" id="reg_date_from" class="m-r-10 p-t-3 m-l-p5"/>
                            <!-- To -->
                            @include('components.datepicker', ['name' => 'filters[date_to]', 'value' => isset($filters['date_to']) && $filters['date_to'] ? $filters['date_to']: ''])
                            <img src="{{ asset('img/calendar.svg') }}" height="30px" id="reg_date_to" width="30px" class="p-t-3 m-l-p5"/>
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
                            <th class="w-20" data-field="subject" data-sortable="true">{{ __('custom.subject') }}</th>
                            <th class="w-10" data-field="org_name" data-sortable="true">{{ __('custom.from') }}</th>
                            <th class="w-10" data-field="created_at" data-sortable="true">{{ __('custom.date') }}</th>
                            <th class="w-10">{{ __('custom.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @if (!empty($messages))
                            @foreach ($messages as $message)
                                <tr class="{{ !$message->read ? 'message-not-read' : ''}}">
                                    <td class="text-left">
                                        <img src="{{ !$message->read ? asset('img/circle-fill.svg') : asset('img/circle-no-fill.svg') }}" height="30px" width="30px" class="p-r-5"/>
                                        {{ $message->subject }}
                                    </td>
                                    <td>{{ $message->sender_org_name }}</td>
                                    <td>{{ $message->created_at }}</td>
                                    <td>
                                        <a href="{{ route('admin.messages', ['id' => $message->parent_id  ? $message->parent_id : $message->id]) }}">
                                            <img src="{{ asset('img/view.svg') }}" height="30px" width="30px"/>
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
                @if (!empty($messages))
                    {{ $messages->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

@endsection


