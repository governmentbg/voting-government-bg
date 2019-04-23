@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')

<div class="row m-t-30">
    @include('components.status')
    @include('components.errors')
    <div class="col-lg-12">
        <form method="get" action="{{ url('/admin/messages') }}">
            <div class="row m-t-15">
                <div class="col-lg-4 display-inline">
                    <label for="filters[status]" class="text-left">{{__('custom.status')}}:</label>
                    <select name="filters[status]" class="ams-dropdown custom-select w-50 js-drop-filter p-t-3">
                        @if (isset($statuses))
                            @foreach($statuses as $statIndex => $statusName)
                                <option value="{{$statIndex}}" {{isset($filters['status']) && $filters['status'] == $statIndex ? 'selected' : ''}}>{{$statusName}}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-lg-5 display-inline">
                    <div class="display-inline ">
                        <label for="filters[subject]">{{__('custom.subject')}}:</label>
                    </div>
                    <input type="text" name="filters[subject]" placeholder="{{__('custom.search')}}" value="{{isset($filters['subject']) && $filters['subject'] ? $filters['subject']: ''}}" class="js-search search-box float-right w-70">
                </div>
            </div>
            <div class="row m-t-10">
                <div class="offset-lg-4 col-lg-5 display-inline">
                    <div class="display-inline">
                        <label for="filters[org_name]">{{__('custom.send_from')}}:</label>
                    </div>
                    <input type="text" name="filters[org_name]" placeholder="{{__('custom.search')}}" value="{{isset($filters['org_name']) && $filters['org_name'] ? $filters['org_name']: ''}}" class="js-search search-box float-right w-70">
                </div>
            </div>
            <div class="row m-t-10">
                <div class="offset-lg-4 col-lg-6 display-inline">
                    <div class="display-inline">
                        <label for="registered_period">{{__('custom.date_period')}}:</label>
                    </div>
                    <div class="display-inline float-right col-lg-9 p-l-none">
                        <!-- From -->
                        @include('components.datepicker', ['name' => 'filters[date_from]', 'value' => isset($filters['date_from']) && $filters['date_from'] ? $filters['date_from']: ''])
                        <img src="{{ asset('img/calendar.svg') }}" height="30px" width="30px" class="m-r-10"/>
                        <!-- To -->
                        @include('components.datepicker', ['name' => 'filters[date_to]', 'value' => isset($filters['date_to']) && $filters['date_to'] ? $filters['date_to']: ''])
                        <img src="{{ asset('img/calendar.svg') }}" height="30px" width="30px"/>
                    </div>
                </div>
            </div>
        </form>
        <div class="table-wrapper m-t-60">
            <div class="table-responsive">
                <table class="table table-striped ams-table voting-tours-list" data-toggle="table">
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
                                <tr>
                                    <td>{{ $message->subject }}</td>
                                    <td>{{ $message->sender_org_name }}</td>
                                    <td>{{ $message->created_at }}</td>
                                    <td>
                                        <a href="{{ route('admin.messages', ['id' => $message->id]) }}"><img src="{{ asset('img/edit.svg') }}" height="30px" width="30px"/></a>
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


