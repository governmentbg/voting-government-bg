
@extends('layouts.app')

@section('content')
    @include('partials.user-nav-bar')
    @include('components.breadcrumbs')
    <div class="container chat chat-list">
        <div class="row">
            <div class="col-lg-12">
                @include('components.status')
                @include('components.errors')
                <form method="POST" action="{{ route('organisation.messages.send') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <p class="col-form-label inline-block">{{ __('custom.subject') }}:</p><br>
                        <input name="subject" class="p-t-3 input-box" value="{{ old('subject') }}" maxlength="255">
                        <span class="error">{{ $errors->first('subject') }}</span>
                    </div>

                    <div class="form-group">
                        <label>{{ __('custom.text') }}:</label>
                        <textarea
                            class="txt-area form-control-static add-message"
                            name="new_message"
                            placeholder="{{ __('custom.enter_text') }}"
                            rows="5"
                            maxlength="8000"
                        >{{ old('new_message') }}</textarea>
                        <span class="error">{{ $errors->first('body') }}</span>
                    </div>

                    <div class="form-group">
                        @include('components.fileinput', ['title' => __('custom.applied_files'), 'name' => 'files[]'])
                        @if ($errors->has('reattach_files'))
                            <span class="error">{{ $errors->first('reattach_files') }}</span>
                        @endif
                    </div>

                    <div class="float-right p-t-5">
                        @include('components.button', ['buttonLabel' => __('custom.send')])
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
