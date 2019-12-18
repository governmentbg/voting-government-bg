<div class="navbar navbar-expand-lg navbar-expand-sm justify-content-end {{ !auth()->check() ? 'p-b-none' : '' }}">
    @if (auth()->guard('backend')->check())
        <a
            href="{{ route('admin.org_list') }}"
            class="{{ Route::currentRouteName() == 'admin.org_list' ? 'c-darkBlue' : 'color-black' }} p-r-20 f-s-21 text-decoration-none"
        >{{ __('custom.profile') }}</a>
    @endif
    @if (isset($showLinks) && !empty($showLinks))
        @if (isset($showLinks['registered']) && $showLinks['registered'])
            <div class="p-r-20 sm-view">
                <a href="{{ route('list.registered') }}" class="f-s-21 text-decoration-none {{ Route::currentRouteName() == 'list.registered' ? : 'color-black' }}">
                    {{ __('custom.registered') }}
                </a>
            </div>
        @endif
        @if (isset($showLinks['candidates']) && $showLinks['candidates'])
            <div class="{{ auth()->check() && !isset($showLinks['voted']) ? 'p-r-none' : 'p-r-20 sm-view' }}">
                <a href="{{ route('list.candidates') }}" class="f-s-21 text-decoration-none {{ Route::currentRouteName() == 'list.candidates' ? : 'color-black' }}">
                    {{ __('custom.candidates') }}
                </a>
            </div>
        @endif
        @if (isset($showLinks['voted']) && $showLinks['voted'])
            <div class="{{ auth()->check() && !isset($showLinks['ranking']) ? 'p-r-none' : 'p-r-20 sm-view' }}">
                <a href="{{ route('list.voted') }}" class="f-s-21 text-decoration-none {{ Route::currentRouteName() == 'list.voted' ? : 'color-black' }}">
                    {{ __('custom.voted') }}
                </a>
            </div>
        @endif
        @if (isset($showLinks['ranking']) && $showLinks['ranking'])
            <div class="{{ auth()->guard('backend')->check() ? 'p-r-20 sm-view' : '' }}">
                <a href="{{ route('list.ranking') }}" class="f-s-21 text-decoration-none {{ Route::currentRouteName() == 'list.ranking' ? : 'color-black' }}">
                    {{ __('custom.ranking') }}
                </a>
            </div>
        @endif
    @endif
    @if ( auth()->guard('backend')->check())
        <div class="p-r-20">
            <a href="{{ route('admin.logout') }}" class="f-s-21 color-black text-decoration-none">{{ __('custom.exit') }}</a>
        </div>
    @endif
</div>
@if (!auth()->check())
    <hr class="hr-thick">
@endif
