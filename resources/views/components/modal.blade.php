<div class="modal inmodal fade" id="info" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog {{ isset($class) ? $class : '' }}">
        <div class="modal-content">
            <div class="p-15 p-b-none">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true" class="c-darkBlue modal-x"><b>&times;</b></span><span class="sr-only">Close</span></button>
                <h4 class="modal-title c-darkBlue">{{ $title }}</h4>
                <hr class="hr-small">
            </div>
            @if (isset($bodyInclude))
                @include($bodyInclude)
            @else
                <div class="modal-body">
                    {{ $body }}
                </div>
            @endif
            <div class="modal-footer border-none">
                <button type="button" class="btn btn-primary login-btn b-c-darkRed" data-dismiss="modal">{{ __('custom.close') }}</button>
                @if (isset($submit))
                    <button type="submit" class="btn btn-primary login-btn" id="dataConfirmOK" data-dismiss="modal">{{ __('custom.confirm') }}</button>
                @endif
            </div>
        </div>
    </div>
</div>
