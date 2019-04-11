<div class="modal inmodal fade" id="info" tabindex="-1" role="dialog"  aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="p-15">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">{{$title}}</h4>
            </div>
            <div class="modal-body">
               {{$body}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary login-btn" data-dismiss="modal">{{ __('Cancel') }}</button>
            </div>
        </div>
    </div>
</div>
