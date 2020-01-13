@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')

@include('components.breadcrumbs')

<div class="row m-r-none m-l-none">
    @include('components.status')
    <div class="col-lg-12">
        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="table table-striped ams-table voting-tours-list">
                    <thead>
                        <tr>
                            <th class="w-15">{{ __('custom.status') }}</th>
                            <th class="w-50 text-left">{{ __('custom.org_name') }}</th>
                            <th class="w-20">{{ __('custom.end_date') }}</th>
                            <th class="w-15">{{ __('custom.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($votingTours as $tour)
                            <tr>
                                <td>
                                    @if ($tour->status == App\VotingTour::STATUS_FINISHED)
                                        <img src="{{ asset('img/cross.svg') }}" height="30px" width="30px"/>
                                    @else
                                        <img src="{{ asset('img/tick.svg') }}" height="30px" width="30px"/>
                                    @endif
                                </td>
                                <td class="text-left">{{$tour->name}}</td>
                                <td>{{isset($tour->updated_at) ? date('Y-m-d H:i', strtotime($tour->updated_at)) : ''}}</td>
                                <td>
                                    @if ($tour->status == App\VotingTour::STATUS_FINISHED)
                                        <a
                                            href="{{ route('admin.ranking', ['id' => $tour->id])}}"><img src="{{ asset('img/star.svg') }}" height="30px" width="50px"
                                            title="{{ __('custom.ranking') }}"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                        ></a>
                                    @else
                                        <a
                                            href="{{route('admin.voting_tour.edit', ['id' => $tour->id])}}"
                                            title="{{ __('custom.edit') }}"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                            class="m-r-15"
                                        >
                                            <img src="{{ asset('img/edit.svg') }}" height="30px" width="30px"/>
                                        </a>
                                    @endif
                                    <a
                                        href="{{ route('admin.actions_history', ['voting_tour_id' => $tour->id])}}"
                                    >
                                        <img
                                            src="{{ asset('img/clock.png') }}"
                                            height="30px" width="30px"
                                            title="{{ __('custom.actions_history') }}"
                                            data-toggle="tooltip"
                                            data-placement="top"
                                        >
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-12 text-right">
        <a class="btn btn-primary" href="{{ route('admin.voting_tour.create') }}">{{ __('custom.new_voting_tour') }}</a>
    </div>
</div>

<div class="row">
    <div class="p-b-15"></div>
</div>
@php 
    http2_push_image('/img/cross.svg');
    http2_push_image('/img/star.svg');
    http2_push_image('/img/edit.svg');
    http2_push_image('/img/clock.svg');
@endphp
@endsection
