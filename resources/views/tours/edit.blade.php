@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')

@include('components.breadcrumbs')

<div class="container">
    <div class="row">
        <div class="col-lg-10 offset-lg-2 col-md-11 offset-md-1 col-sm-12">
            <h2 class="color-dark"><b>{{ 'име на организацията' }}</b></h2>
            <form method="POST">
                <div class="form-group row">
                    <label for="status" class="col-sm-4 col-xs-12"> {{ __('custom.status') }}:</label>
                    <div class="col-sm-8">
                        <select name="status">
                            @foreach(\App\VotingTour::getStatuses() as $key => $status)
                                <option value="{{$key}}">{{$status}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="fname" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.updated_at') }}:</label>
                    <div class="col-sm-8">
                        <span>{{ date('Y-m-d H:i:s') }}</span>
                    </div>
                </div>
                
                <div class="form-group row">
                    <label for="fname" class="col-sm-4 col-xs-12 col-form-label"> {{ __('custom.updated_by') }}:</label>
                    <div class="col-sm-8">
                        {{ 'име на потребител направил промяната' }}
                    </div>
                </div>
                
                <div class="col-xs-12 text-center">
                    <button class="btn btn-primary" type="submit">{{ __('custom.save') }}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" tabindex="-1" role="dialog" id='confirmEmailSending'>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('custom.voting') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ __('messages.status_change_confirmation')}}</p>
                    @include('components.checkbox', ['name' => 'send_emails', 'label' => __('messages.send_n_messages', ['count' => 1332])])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('custom.cancel')}}</button>
                    <button type="button" class="btn btn-primary">{{ __('custom.confirm')}}</button>
                </div>
            </div>
        </div>
    </div>
    
</div>

@endsection

