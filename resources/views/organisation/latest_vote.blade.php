@extends('layouts.app')

@section('content')
@include('partials.user-nav-bar')
@include('components.breadcrumbs')
@include('components.status')

@if (!empty($orgList))
    <div class="row">
        <div class="col-lg-12">
            <div class="offset-lg-3 col-lg-4 display-inline p-l-none"><img src="{{ asset('img/tick.svg') }}" height="35px" width="35px" class="m-b-15"/></div>
            <div class="col-lg-3 display-inline"><h2 class="color-dark display-inline h2-custom"><b>{{ __('custom.your_vote_was_successful') }}</b></h2></div>
        </div>
    </div>
    <div class="row m-b-15">
    </div>
    <div class="row">
        <div class="col-lg-12">
            @if (isset($latestVoteData))
                @php
                    $i = 1;
                @endphp
                <div class="offset-lg-3 col-lg-6 vote-box-outline">
                    @foreach ($orgList as $index => $singleOrg)
                        @if (in_array($singleOrg->id, $latestVoteData))
                            <div class="c-darkBlue p-2">
                                @php
                                    if (strlen($i) < 2) {
                                        $padNumber = 'p-l-13';
                                    } else {
                                        $padNumber = '';
                                    }
                                @endphp
                                <div class="display-inline f-s-21 p-r-15 {{ $padNumber }}"><b>{{ $i }}</b></div>
                                <div class="display-inline p-r-15"><img src="{{ asset('img/tick.svg') }}" height="30px" width="30px" class="m-b-8"/></div>
                                <div class="display-inline f-s-21 ">{{ $singleOrg->eik .' - '. $singleOrg->name }}</div>
                                @php
                                    $i++;
                                @endphp
                            </div>
                        @endif
                    @endforeach
                </div>
            <div class="offset-lg-3 col-lg-6 text-right p-r-none m-t-20">
                <form method="GET" action="{{ route('organisation.vote') }}">
                    <button
                        type="submit"
                        class="btn btn-primary b-c-darkRed login-btn"
                        name="change"
                        value="1"
                    >{{ __('custom.change') }}</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    @php http2_push_image('/img/tick.svg'); @endphp
@else
    <div class="row">
        <div class="col-lg-12 display-flex justify-center">
            <h2 class="color-dark h2-custom"><b>{{ __('custom.no_tours_available') }}</b></h2>
        </div>
    </div>
@endif

@endsection
