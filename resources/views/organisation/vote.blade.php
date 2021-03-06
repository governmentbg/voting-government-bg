@extends('layouts.app')

@section('content')
@include('partials.user-nav-bar')
@include('components.breadcrumbs')
@include('components.status')

@if (!empty($orgList))
    <div class="row p-l-15 p-r-15">
        <div class="offset-lg-1 offset-md-1 col-lg-11 col-md-11 col-sm-12">
            <div class="row">
                <h2 class="color-dark"><b>{{ __('custom.voting_title') }}</b></h2>
            </div>
            <form method="POST" class="m-t-20" id="js-voteform" action="{{ route('organisation.vote_action') }}" data-max-votes="{{ $maxVotes }}">
                @include('components.modal', [
                    'title' => __('custom.confirm_vote'),
                    'body' => '',
                    'submit' => true
                ])
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-5 vote-box-outline">
                        <h3 class="m-t-15">{{ __('custom.registered_participants') }}</h3>
                        <hr class="hr-small">
                        <div class="display-flex m-b-8">
                            <input type="text" name="org_name" id="filter_org" placeholder="{{__('custom.search')}}" autocomplete="off"
                                class="search-box vote-search-height float-right w-100 no-outline">
                        </div>
                        <div class="nano vote-height m-b-15">
                            <select name="organisations" multiple="multiple" id="vote_organisations" class="vote-box nano-content" size="14">
                                @if (!empty($orgList))
                                    @foreach ($orgList as $singleOrg)
                                        @if (!empty($latestVoteData))
                                            @if (!in_array($singleOrg->id, $latestVoteData))
                                                <option value="{{ $singleOrg->id }}" title="{{ $singleOrg->name .' - '. $singleOrg->eik }}">
                                                    {{ $singleOrg->name .' - '. $singleOrg->eik }}
                                                </option>
                                            @endif
                                        @else
                                            <option value="{{ $singleOrg->id }}" title="{{ $singleOrg->name .' - '. $singleOrg->eik }}">
                                                {{ $singleOrg->name .' - '. $singleOrg->eik }}
                                            </option>
                                        @endif
                                    @endforeach
                                @else
                                    <option disabled>{{ __('custom.org_list_not_available') }}</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-1 arrows-position">
                        <div class="display-flex justify-center">
                            <img class="arrow-right c-darkBlue c-pointer" src="{{ asset('img/vote_arrow.svg') }}" id="js-add-org" height="60px" width="60px" />
                        </div>
                        <div class="p-t-15 display-flex justify-center">
                            <img class="arrow-left c-darkBlue c-pointer" src="{{ asset('img/vote_arrow.svg') }}"  id="js-remove-org" height="60px" width="60px" />
                        </div>
                    </div>
                    <div class="col-md-5 vote-box-outline">
                        <h3 class="m-t-15">{{ __('custom.selected_participants') }} {{ untrans('custom.max_n', 1, ['count' => $maxVotes]) }}</h3>
                        <hr class="hr-small">
                        <div class="m-t-53 nano vote-height m-b-15">
                            <select name="votefor[]" multiple="multiple" id="votefor" class="vote-box nano-content" size="13">
                                @if (!empty($latestVoteData))
                                    @foreach ($orgList as $singleOrg)
                                        @if (in_array($singleOrg->id, $latestVoteData))
                                            <option value="{{ $singleOrg->id }}" title="{{ $singleOrg->name .' - '. $singleOrg->eik }}">
                                                {{ $singleOrg->name .' - '. $singleOrg->eik }}
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row mt-2">
                    <div class="col-lg-11 col-md-11 text-center">
                        @include('components.button', ['buttonLabel' => __('custom.vote'), 'id' => 'votebtn', 'disabled' => true])
                    </div>
                </div>
            </form>
        </div>
    </div>
    @php http2_push_image('/img/vote_arrow.svg'); @endphp
@elseif (!empty($infoLabel))
    <div class="row">
        <div class="col-lg-12 display-flex justify-center">
            <h2 class="color-dark h2-custom"><b>{{ __($infoLabel) }}</b></h2>
        </div>
    </div>
@endif

@endsection
