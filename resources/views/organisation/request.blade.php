
@extends('layouts.app')

@section('content')
        @include('partials.user-nav-bar')
        @include('components.breadcrumbs')
        <div class="container chat chat-list">
            <div class="row">
                <div class="col-lg-12 p-l-40"><h3>{{ $parent->subject }}</h3></div>
            </div>

            @foreach($messages as $index => $message)
                @include('components.chat', [
                    'isSender' => isset($message->sender_org_id),
                    'message' => $message->body,
                    'orgname' => isset($message->sender_org_id)?  $message->sender_org_name : $message->sender_user_name,
                    'messageTime' => $message->created_at,
                    'files' => !empty($message->files) ? $message->files : [],
                    'read' => $message->read,
                    'isAdmin' => false,
                ])
            @endforeach
            <div class="row">
                <div class="col-lg-12">
                    @include('components.errors')
                    <form method="POST" action="{{ route('organisation.messages.send', ['id' => $parent->id])}}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" name="subject" value="{{ $parent->subject }}">

                        <textarea class="txt-area" name="new_message" rows="5" placeholder="{{__('custom.enter_text')}}"></textarea>
                        
                        <div class="file-container">                            
                            @include('components.fileinput', ['title' => __('custom.applied_files'), 'name' => 'files[]'])
                            <div class="js-plus-file-upl">
                                <a href="#"><img class="display-inline m-b-8 rotate-180 p-r-5" src="{{ asset('img/plus.svg') }}" height="35px" width="30px" /></a>
                            </div>
                        </div>
                        
                        <div class="float-right p-t-5">
                            @include('components.button', ['buttonLabel' => __('custom.send')])
                        </div>
                    </form>
                </div>
            </div>
        
        </div>
@endsection
