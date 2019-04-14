@foreach (['danger', 'warning', 'success', 'info'] as $msg)
    @if (Session::has('alert-' . $msg))
        <div class="col-lg-12 col-md-11 col-xs-12 m-t-md">
            <div class="flash-message">
                <p class="alert alert-{{ $msg }}">
                    {{ Session::get('alert-' . $msg) }}
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                </p>
            </div>
        </div>
        @php
            Session::forget('alert-' . $msg);
        @endphp
    @endif
@endforeach
