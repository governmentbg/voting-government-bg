@extends('layouts.app')

@section('content')
@include('partials.user-nav-bar')
@include('components.breadcrumbs')
@include('components.status')

<div class="row">
    <div class="offset-lg-2 col-lg-10">
        @if (!empty($orgList))
            <div class="row">
                <h2 class="color-dark"><b>{{ __('custom.voting_title') }}</b></h2>
            </div>
            <form method="POST" class="m-t-20" id="js-voteform" action="{{ route('organisation.vote_action') }}">
                @include('components.modal', [
                    'title' => __('custom.confirm_vote'),
                    'body' => '',
                    'submit' => true
                ])
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-4 vote-box-outline">
                        <h3 class="m-t-15">{{ __('custom.registered_participants') }}</h3>
                        <hr class="hr-small">
                        <div class="display-flex m-b-8">
                            <input type="text" name="org_name" id="filter_org" placeholder="{{__('custom.search')}}" autocomplete="off" class="search-box float-right w-100 no-outline">
                        </div>
                        <div class="nano h-65 m-b-15">
                        <select name="organisations" multiple="multiple" id="vote_organisations" class="vote-box nano-content" size="14">
                            @if (!empty($orgList))
                                @foreach ($orgList as $singleOrg)
                                    @if (!empty($latestVoteData))
                                        @if (!in_array($singleOrg->id, $latestVoteData))
                                            <option value="{{ $singleOrg->id }}">{{ $singleOrg->name .' - '. $singleOrg->eik }}</option>
                                        @endif
                                    @else
                                        <option value="{{ $singleOrg->id }}">{{ $singleOrg->name .' - '. $singleOrg->eik }}</option>
                                    @endif
                                @endforeach
                            @else
                                <option disabled>{{ __('custom.org_list_not_available') }}</option>
                            @endif
                        </select>
                        </div>
                    </div>
                    <div class="col-md-1 p-l-37 arrows-position">
                        <div>
                            <img class="arrow-right c-darkBlue" src="{{ asset('img/vote_arrow.svg') }}" id="js-add-org" height="60px" width="60px" />
                        </div>
                        <div class="p-t-15">
                            <img class="arrow-left c-darkBlue" src="{{ asset('img/vote_arrow.svg') }}"  id="js-remove-org" height="60px" width="60px" />
                        </div>
                    </div>
                    <div class="col-md-4 vote-box-outline">
                        <h3 class="m-t-15">{{ __('custom.selected_participants') }} {{ __('custom.max_14') }}</h3>
                        <hr class="hr-small">
                        <div class="m-t-53 nano h-65 m-b-15">
                            <select name="votefor[]" multiple="multiple" id="votefor" class="vote-box nano-content" size="13">
                                @if (!empty($latestVoteData))
                                    @foreach ($orgList as $singleOrg)
                                        @if (in_array($singleOrg->id, $latestVoteData))
                                            <option value="{{ $singleOrg->id }}">{{ $singleOrg->name .' - '. $singleOrg->eik }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="m-l-percent col-sm-3 p-r-none text-center">
                        @include('components.button', ['buttonLabel' => __('custom.vote'), 'id' => 'votebtn', 'disabled' => true])
                    </div>
                </div>
            </form>
        @else
            <div class="row">
                <div class="col-lg-12 display-flex justify-center w-100">
                    <h2 class="color-dark h2-custom"><b>{{ __('custom.no_tours_available') }}</b></h2>
                </div>
            </div>
        @endif
    </div>
</div>

@endsection
