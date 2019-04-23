<div class="row">
    @if ($isSender)
        <div class="col-lg-9 offset-lg-3 chat-reciever">
            <div class="reciever-info">
                <img class="display-inline m-b-8" src="{{ asset('img/request-dark.svg') }}" height="30px" width="30px"/>
                <h3 class="display-inline p-l-5 p-t-5">{{$orgname}}</h3>
            </div>
            <div class="border-chat-receiver">
                <div class="chat-container">
                    <p class="p-t-5">{{$message}}</p>
                    <span class="msg-time">
                        {{$messageTime}}
                        @if ($read)
                            <img class="display-inline m-b-8 p-l-5" src="{{ asset('img/tick.svg') }}" title="{{ __('custom.status_read')}}: {{$read}}" height="20px" width="20px"/>
                        @endif</span>
                    </span>
                </div>
            </div>
            @if (!empty($files))
                <div class="file-container">
                    <h3 class="color-black">{{__('custom.applied_files')}}</h3>
                    @foreach($files as $file)
                        <a 
                            href="{{ $isAdmin? route('admin.fileDowload', ['id' => $file->id]) : route('fileDowload', ['id' => $file->id]) }}" 
                            target="_blank"
                        >
                            {{$file->name}}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="col-lg-9 p-t-5 chat-sender">
            <div class="sender-info">
                <img class="display-inline m-b-8" src="{{ asset('img/request-light.svg') }}" height="30px" width="30px"/>
                <h3 class="display-inline p-l-5 p-t-5">{{$orgname}}</h3>
            </div>
            <div class="border-chat-sender">
                <div class="chat-container">
                    <p class="p-t-5">{{$message}}</p>
                    <span class="msg-time">
                        {{$messageTime}}
                        @if ($read)
                        <img class="display-inline m-b-8 p-l-5" src="{{ asset('img/tick.svg') }}" title="{{ __('custom.status_read')}}: {{$read}}" height="20px" width="20px"/>
                        @endif
                    </span>
                </div>
            </div>
            @if (!empty($files))
                <div class="file-container">
                    <h3 class="color-black">{{__('custom.applied_files')}}</h3>
                    @foreach($files as $file)
                        <a 
                            href="{{ $isAdmin ? route('admin.fileDowload', ['id' => $file->id]) : route('fileDowload', ['id' => $file->id]) }}" 
                            target="_blank"
                        >
                            {{$file->name}}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
