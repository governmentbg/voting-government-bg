
@extends('layouts.app')

@section('content')
    @include('partials.admin-nav-bar')
    @include('components.breadcrumbs')

    @if (!empty($parent))
        <div class="container chat chat-list p-b-30">
            <div class="row">
                <div class="col-lg-12"><h3>{{ $parent->subject }}</h3></div>
            </div>

            @foreach ($messages as $message)
                @if ($message->id != $parent->id)
                    <a name="{{ $message->id }}"></a>
                @endif
                @include('components.chat', [
                    'isSender' => !isset($message->sender_org_id),
                    'message' => $message->body,
                    'orgname' => isset($message->sender_org_id) ? $message->sender_org_name : $message->sender_user_name,
                    'messageTime' => $message->created_at,
                    'files' => !empty($message->files) ? $message->files : [],
                    'read' => $message->read,
                    'isAdmin' => true,
                ])
            @endforeach

            <div class="row">
                <div class="col-lg-12">
                    <form method="POST" action="{{ route('admin.messages.send', ['id' => $parent->id]) }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="subject" value="{{ $parent->subject }}">
                        <input type="hidden" name="recipient_org_id" value="{{ $parent->sender_org_id }}">

                        <div class="form-group p-b-none m-t-40">
                            <a name="error"></a>
                            @include('components.errors')
                        </div>

                        <div class="form-group">
                            <textarea
                                class="txt-area p-l-10"
                                name="new_message"
                                placeholder="{{ __('custom.enter_text') }}"
                                rows="5"
                                maxlength="8000"
                            >{{ old('new_message') }}</textarea>
                            <span class="error">{{ $errors->first('body') }}</span>
                        </div>

                        <div class="float-right p-t-5">
                            @include('components.button', ['buttonLabel' => __('custom.send')])
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
