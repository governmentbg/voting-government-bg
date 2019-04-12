<div class="flex-nav-items p-t-15">
@if (isset($showLinks) && !empty($showLinks))
    @if (isset($showLinks['registered']) && $showLinks['registered'])
    <div class="p-r-15">
        <a href="{{ route('list.registered') }}" class="f-s-21 text-decoration-none {{ Route::currentRouteName() == 'list.registered' ? : 'color-black' }}">
            {{ __('custom.registered') }}
        </a>
    </div>
    @endif
    @if (isset($showLinks['candidates']) && $showLinks['candidates'])
    <div class="p-r-15">
        <a href="{{ route('list.candidates') }}" class="f-s-21 text-decoration-none {{ Route::currentRouteName() == 'list.candidates' ? : 'color-black' }}">
            {{ __('custom.candidates') }}
        </a>
    </div>
    @endif
    @if (isset($showLinks['voted']) && $showLinks['voted'])
    <div class="p-r-15">
        <a href="{{ route('list.voted') }}" class="f-s-21 text-decoration-none {{ Route::currentRouteName() == 'list.voted' ? : 'color-black' }}">
            {{ __('custom.voted') }}
        </a>
    </div>
    @endif
    @if (isset($showLinks['ranking']) && $showLinks['ranking'])
    <div class="p-r-15">
        <a href="{{ route('list.ranking') }}" class="f-s-21 text-decoration-none {{ Route::currentRouteName() == 'list.ranking' ? : 'color-black' }}">
            {{ __('custom.ranking') }}
        </a>
    </div>
    @endif
@endif
</div>
<hr class="hr-thick">
