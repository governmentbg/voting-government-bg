
@extends('layouts.app')

@php
    $chatMembers = ['sender' => 1, 'receiver' => 2];
    $messagesArray = [[
        'org_id' => 1,
        'message' => 'Hello Im the sender',
        'orgname' => 'Организация на изпращачите',
        'messageTime' => '23:21',
        'file' => 'Удостоверение за удостоверение.pdf',
        'read' => true
        ],
        [
        'org_id' => 2,
        'message' => 'Hello Im the receiver',
        'orgname' => 'Организация на получателите',
        'messageTime' => '23:22',
        'file' => 'Удостоверение за удостоверение2.pdf',
        'read' => true
        ],
        [
        'org_id' => 1,
        'message' => 'Hello Im the sender again',
        'orgname' => 'Организация на изпращачите',
        'messageTime' => '23:23',
        'read' => false
        ],
        [
        'org_id' => 1,
        'message' => 'Hello Im the sender again yet again',
        'orgname' => 'Организация на изпращачите',
        'messageTime' => '23:24',
        'read' => false
        ]]
        ;
@endphp

@section('content')
        @include('partials.user-nav-bar')
        @include('components.breadcrumbs')
        <div class="row p-b-15">
            <div class="col-lg-12 p-l-40"><h3>{{ __('custom.request_for_resolution') }}</h3></div>
        </div>

        @foreach($messagesArray as $index => $singleMessage)
            @include('components.chat', [
                'chatMembers' => $chatMembers,
                'member' => $singleMessage['org_id'],
                'message' => $singleMessage['message'],
                'orgname' => $singleMessage['orgname'],
                'messageTime' => $singleMessage['messageTime'],
                'file' => !empty($singleMessage['file']) ? $singleMessage['file'] : '',
                'read' => $singleMessage['read']
            ])
        @endforeach
    <div class="row">
        <div class="col-lg-7 p-l-40 p-t-15">
            <textarea class="txt-area" name="new_message" rows="5" placeholder="{{__('custom.enter_text')}}"></textarea>
            <div class="file-container">
                @include('components.fileinput', ['title' => __('custom.applied_files')])
                <div>
                    <a href="#"><img class="display-inline m-b-8 rotate-180 p-r-5" src="{{ asset('img/plus.svg') }}" height="35px" width="30px" /></a>
                </div>
            </div>
            <div class="float-right p-t-5">
                @include('components.button', ['buttonLabel' => __('custom.send')])
            </div>
        </div>
    </div>
@endsection
