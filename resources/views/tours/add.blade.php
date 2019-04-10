@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')
@include('components.breadcrumbs')

<div class="container center-flex">
    <div class="col-lg-12 col-md-11 col-xs-12 col-lg-offset-1 m-t-md">
        <div class="row justify-center">
            <div class="col-md-10">
                <div>
                    <h2 class="color-dark"><b>{{ __('custom.voting_tour_create') }}</b></h2>
                </div>
                <form method="POST" class="m-t-20">
                    {{ csrf_field() }}
                    <div class="form-group row required">
                        <label for="name" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.name') }}:</label>
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
                        <label for="new_password" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.status') }}:</label>
                        <div class="col-sm-8">
                            <select name="status" class="ams-dropdown custom-select">
                                @foreach(\App\VotingTour::getStatuses() as $key => $status)
                                    <option value="{{$key}}">{{$status}}</option>
                                @endforeach
                            </select>
                            <span class="error">{{ $errors->first('status') }}</span>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12 col-xs-6 p-r-none text-right">
                            @include('components.button', ['buttonLabel' => __('custom.save')])
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
