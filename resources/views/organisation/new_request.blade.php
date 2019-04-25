
@extends('layouts.app')

@section('content')
        @include('partials.user-nav-bar')
        @include('components.breadcrumbs')
        <div class="container chat chat-list">
            <div class="row">
                <div class="col-lg-12">
                    @include('components.errors')
                    @include('components.status')
                    <form method="POST" action="{{ route('organisation.messages.send') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <p class="col-form-label inline-block">{{ __('custom.subject')}}:</p><br>
                            <input name="subject" class="p-t-3 input-box" value="{{ old('subject') }}">
                            <span class="error">{{ $errors->first('subject') }}</span>
                        </div>
                       
                        <div class="form-group">
                            <label>{{ __('custom.text')}}:</label>
                            <textarea class="txt-area form-control-static add-message" name="new_message" rows="5" placeholder="{{__('custom.enter_text')}}"></textarea>
                            <span class="error">{{ $errors->first('body') }}</span>
                        </div>
                                                                        
                        @include('components.fileinput', ['title' => __('custom.applied_files'), 'name' => 'files[]'])
                                            
                        <div class="float-right p-t-5">
                            @include('components.button', ['buttonLabel' => __('custom.send')])
                        </div>
                    </form>
                </div>
            </div>
        
        </div>
@endsection
