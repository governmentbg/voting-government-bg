@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')

@include('components.breadcrumbs')

<div class="container center-flex">
    <div class="col-lg-12 col-md-11 col-xs-12 col-lg-offset-1 m-t-md">
        <div class="row justify-center">
            <div class="col-md-10">
                <div>
                    <h2 class="color-dark"><b>{{ __('custom.create_voting_tour') }}</b></h2>
                </div>
                <form method="POST" class="m-t-lg p-sm" action="{{route('admin.voting_tour.store')}}">
                    {{ csrf_field() }}
                    @include('components.errors')
                    <div class="form-group row required">
                        <label for="fname" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.org_name') }}:</label>
                        <div class="col-sm-8">
                            <input
                                type="text"
                                class="input-box"
                                name="name"
                                value=""
                            >
                            <span class="error">{{ $errors->first('name') }}</span>
                        </div>
                    </div>
                    
                    <div class="form-group row required">
                        <label for="status" class="col-sm-4 col-xs-12"> {{ __('custom.status') }}:</label>
                        <div class="col-sm-8">
                            <select name="status" class="ams-dropdown custom-select">
                                @foreach(\App\VotingTour::getStatuses() as $key => $status)
                                <option value="{{$key}}">{{$status}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-10 text-center">
                    <div class="form-group mt-4">
                    @include('components.button', ['buttonLabel' => __('custom.new_voting_tour')])
                    </div>
                </div>                            
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

