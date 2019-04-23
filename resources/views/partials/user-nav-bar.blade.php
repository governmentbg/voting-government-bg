<div class="flex-nav-items p-t-15">
    @if(auth()->check())
    <div class="p-r-20"><span class="f-s-21">{{ auth()->user()->username }}</span></div>
    @endif
    <div class="p-r-15">
        <a
        href="{{route('organisation.vote')}}"
        class="f-s-21 color-black text-decoration-none {{ Route::currentRouteName() == 'organisation.vote' ? 'c-darkBlue' : 'color-black' }}">{{ __('custom.votingmenu') }}</a>
    </div>
    <div class="p-r-15">
        <a
            href="{{ route('organisation.change_password')}}"
            class="f-s-21 color-black text-decoration-none {{ Route::currentRouteName() == 'organisation.change_password' ? 'c-darkBlue' : 'color-black' }}"
        >{{ __('custom.settings') }}</a>
    </div>
    @if(auth()->check())
    <div class="p-r-20">
        <form id="logout" action="{{ route('logout') }}" method="POST">
            {{ csrf_field() }}
            <a href="#" onclick="document.getElementById('logout').submit();" class="f-s-21 color-black text-decoration-none">{{ __('custom.exit') }}</a>
        </form>
    </div>
    @endif
</div>
<hr class="hr-thick">
