
@extends('layouts.app')

@section('content')
        @include('partials.admin-nav-bar')
        @include('components.breadcrumbs')
        <div class="container chat chat-list">    
            <div class="row">
                <div class="col-lg-12 p-l-40"><h3>{{ $parent->subject }}</h3></div>
            </div>

            @foreach($messages as $index => $message)
                @include('components.chat', [
                    'isSender' => !isset($message->sender_org_id),
                    'message' => $message->body,
                    'orgname' => isset($message->sender_org_id)?  $message->sender_org_name : $message->sender_user_name,
                    'messageTime' => $message->created_at,
                    'files' => !empty($message->files) ? $message->files : [],
                    'read' => $message->read,
                    'isAdmin' => true,
                ])
            @endforeach
        <div class="row">
            <div class="col-lg-12">
                @include('components.errors')
                <form method="POST" action="{{ route('admin.messages.send', ['id' => $parent->id])}}">
                    {{ csrf_field() }}
                    <input type="hidden" name="subject" value="{{ $parent->subject }}">
                    <input type="hidden" name="recipient_org_id" value="{{ $parent->sender_org_id }}">
                    
                    <textarea class="txt-area" name="new_message" rows="5" placeholder="{{__('custom.enter_text')}}"></textarea>
                    <div class="float-right p-t-5">
                        @include('components.button', ['buttonLabel' => __('custom.send')])
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
