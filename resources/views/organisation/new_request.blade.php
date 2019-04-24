
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
                        <p class="col-form-label inline-block">{{ __('custom.subject')}}:</p><br>
                        <select name="subject" class="ams-dropdown custom-select p-t-3" id="msg-subject">
                            @if (isset($statuses))
                                @foreach($statuses as $statIndex => $statusName)
                                    <option value="{{$statusName}}">{{$statusName}}</option>
                                @endforeach
                            @endif
                        </select>  
                       

                        <textarea class="txt-area" name="new_message" rows="5" placeholder="{{__('custom.enter_text')}}"></textarea>
                                                                        
                        @include('components.fileinput', ['title' => __('custom.applied_files'), 'name' => 'files[]'])
                                            
                        <div class="float-right p-t-5">
                            @include('components.button', ['buttonLabel' => __('custom.send')])
                        </div>
                    </form>
                </div>
            </div>
        
        </div>
@endsection
