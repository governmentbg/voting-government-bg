
@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')

@include('components.breadcrumbs')

<div class="col-lg-12">
    <table class="table table-striped table-responsive ams-table">
        <thead>
            <tr>
                <th class="w-5">{{ __('custom.status') }}</th>
                <th class="w-30">{{ __('custom.org_name') }}</th>
                <th class="w-25">{{ __('custom.end_date') }}</th>
                <th class="w-1">{{ __('custom.operations') }}</th>
        </tr>
        </thead>
        <tbody class="text-center">
            @foreach($votingTours as $tour)
                <tr>
                    <td>
                        @if($tour->status == App\VotingTour::STATUS_FINISHED)
                            <img src="{{ asset('img/cross.svg') }}" height="30px" width="30px"/>
                        @else
                            <img src="{{ asset('img/tick.svg') }}" height="30px" width="30px"/>
                        @endif
                    </td>
                    <td class="text-left">{{$tour->name}}</td>
                    <td>{{date('Y-m-d H:i', strtotime($tour->updated_at))}}</td>
                    <td> 
                        @if($tour->status == App\VotingTour::STATUS_FINISHED)
                            <a href="#"><img src="{{ asset('img/star.svg') }}" height="30px" width="50px"/></a>
                        @else
                            <a href="{{route('admin.voting_tour.edit', ['id' => $tour->id])}}">
                                <img src="{{ asset('img/edit.svg') }}" height="30px" width="30px"/>
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="col-lg-12 text-right p-r-none">
        <button
            type="submit"
            class="btn btn-primary login-btn"
        >{{ __('custom.new_message') }}</button>
    </div>
</div>
@endsection
