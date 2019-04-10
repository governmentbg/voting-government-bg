@extends('layouts.app')

@section('content')
@include('partials.org-nav-bar')
@include('components.breadcrumbs')

<div class="container">
    <div class="row">
        <div class="col-lg-12 col-md-11 offset-md-1 col-sm-12">
            <div>
                <h2 class="color-dark"><b>{{ __('custom.voting_title') }}</b></h2>
            </div>
            <form method="POST" class="m-t-20">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-5">
                        <h3>{{__('custom.registered_participants')}}</h3>
                        <div class="display-flex m-b-8">
                            <input type="text" name="org_name" id="filter_org" placeholder="{{__('custom.search')}}" class="search-box float-right w-50">
                        </div>
                        <select name="organisations" multiple="multiple" id="vote_organisations" class="ams-dropdown custom-select w-50" size="14">
                            <option value="1" >organisation 1</option>
                            <option value="2" >organisation 2</option>
                            <option value="3" >organisation 3</option>
                            <option value="4" >organisation 4</option>
                            <option value="1" >organisation 5</option>
                            <option value="2" >organisation 6</option>
                            <option value="3" >organisation 7</option>
                            <option value="4" >organisation 8</option>
                            <option value="1" >organisation 9</option>
                            <option value="2" >organisation 10</option>
                            <option value="3" >organisation 11</option>
                            <option value="4" >organisation 12</option>
                            <option value="1" >organisation 13</option>
                            <option value="2" >organisation 14</option>
                            <option value="3" >organisation 15</option>
                            <option value="4" >organisation 16</option>
                        </select>
                    </div>
                    <div class="col-md-2 arrows-position m-l-85">
                        <div>
                            <img class="arrow-right c-darkBlue" src="{{ asset('img/vote_arrow.svg') }}" id="js-add-org" height="60px" width="60px" />
                        </div>
                        <div class="p-t-15">
                            <img class="arrow-left c-darkBlue" src="{{ asset('img/vote_arrow.svg') }}"  id="js-remove-org" height="60px" width="60px" />
                        </div>
                    </div>
                    <div class="col-md-5">
                        <h3>{{__('custom.selected_participants')}} {{__('custom.max_14')}}</h3>
                        <div class="m-t-45">
                            <select name="votefor" multiple="multiple" id="votefor" class="ams-dropdown custom-select w-50" size="14">
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-9 col-xs-6 p-r-none text-center">
                        @include('components.button', ['buttonLabel' => __('custom.vote'), 'id' => 'votebtn', 'disabled' => true])
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
