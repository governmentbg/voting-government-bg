<div class="row p-l-25">
    @if ($chatMembers['receiver'] == $member)
        <div class="col-lg-4">
        </div>
        <div class="col-lg-5">
            <img class="display-inline m-b-8" src="{{ asset('img/request-dark.svg') }}" height="30px" width="30px"/>
            <h3 class="display-inline p-l-5 p-t-5">{{$orgname}}</h3>
            <div class="border-chat-receiver">
                <div class="chat-container">
                    <p class="p-t-5">{{$message}}</p>
                    <span class="msg-time">
                        {{$messageTime}}
                        @if ($read)
                            <img class="display-inline m-b-8 p-l-5" src="{{ asset('img/tick.svg') }}" height="20px" width="20px"/>
                        @endif</span>
                    </span>
                </div>
            </div>
            @if (!empty($file))
                <div class="file-container">
                <h3 class="color-black">{{__('custom.applied_files')}}</h3>
                    <a href='#'>{{$file}}</a>
                </div>
            @endif
        </div>
    @else
        <div class="col-lg-6 p-t-5">
            <img class="display-inline m-b-8" src="{{ asset('img/request-light.svg') }}" height="30px" width="30px"/>
            <h3 class="display-inline p-l-5 p-t-5">{{$orgname}}</h3>
            <div class="border-chat-sender">
                <div class="chat-container">
                    <p class="p-t-5">{{$message}}</p>
                    <span class="msg-time">
                        {{$messageTime}}
                        @if ($read)
                            <img class="display-inline m-b-8 p-l-5" src="{{ asset('img/tick.svg') }}" height="20px" width="20px"/>
                        @endif
                    </span>
                </div>
            </div>
            @if (!empty($file))
                <div class="file-container">
                    <h3 class="color-black">{{__('custom.applied_files')}}</h3>
                    <a href='#'>{{$file}}</a>
                </div>
            @endif
        </div>
        <div class="col-lg-5">
        </div>
    @endif
</div>