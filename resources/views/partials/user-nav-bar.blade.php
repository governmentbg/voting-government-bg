<div class="flex-nav-items p-t-15">
    <div class="p-l-25 f-s-21">
        @if (isset($votingTourData))
            @if ($votingTourData->showTick)
                <img src="{{ asset('img/tick.svg') }}" height="30px" width="30px" class="display-inline m-t-12"/>
            @endif
            <h3 class="display-inline">{{ $votingTourData->name .' - '. $votingTourData->statusName }}</h3>
        @endif
    </div>
    <div class="display-flex">
        @if (auth()->check())
            <div class="p-r-20 f-s-21">{{ auth()->user()->username }}</div>
            <div class="p-r-20">
                <a
                    href="{{ route('organisation.vote') }}"
                    class="f-s-21 color-black text-decoration-none {{ in_array(Route::currentRouteName(), [
                        'organisation.vote',
                        'organisation.vote_action'
                    ]) ? 'c-darkBlue' : 'color-black' }}"
                >{{ __('custom.votingmenu') }}</a>
            </div>
            <div class="p-r-20">
                <a
                    href="{{ route('organisation.change_password')}}"
                    class="f-s-21 color-black text-decoration-none {{ in_array(Route::currentRouteName(), [
                        'organisation.change_password',
                        'organisation.settings'
                    ]) ? 'c-darkBlue' : 'color-black' }}"
                >{{ __('custom.settings') }}</a>
            </div>
            <div class="p-r-20">
                <form id="logout" action="{{ route('logout') }}" method="POST">
                    {{ csrf_field() }}
                    <a href="#" onclick="document.getElementById('logout').submit();" class="f-s-21 color-black text-decoration-none">{{ __('custom.exit') }}</a>
                </form>
            </div>
        @endif
    </div>
</div>
<hr class="hr-thick">
