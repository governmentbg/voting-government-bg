<nav class="col-lg-12 navbar navbar-expand-lg navbar-expand-md p-b-none">
    <div class="navbar-collapse order-1 p-r-20">
        @if (!empty($votingTourData))
            @if ($votingTourData->showTick)
                <img src="{{ asset('img/tick.svg') }}" height="30px" width="30px" class="display-inline m-t-12 p-r-5"/>
            @endif
            <h3 class="display-inline">{{ $votingTourData->name .' - '. $votingTourData->statusName }}</h3>
        @endif
        </div>
        <div class="navbar-collapse order-3 justify-content-end align-top mt-md-0 mt-xl-0 mt-4">
        @if (auth()->guard('backend')->check())
            <div class="p-r-20 f-s-21">{{ auth()->guard('backend')->user()->username }}</div>
            <div class="p-r-20 text-truncate">
                <a
                    href="{{ route('admin.org_list') }}"
                    class="f-s-21 color-black text-decoration-none {{ in_array(Route::currentRouteName(), [
                        'admin.org_list',
                        'admin.org_edit'
                    ]) ? 'c-darkBlue' : 'color-black' }}"
                >{{ __('custom.registered_orgs') }}</a>
            </div>
            <div class="p-r-20">
                <a
                    href="{{ route('admin.messages.list') }}"
                    class="f-s-21 color-black text-decoration-none {{ in_array(Route::currentRouteName(), [
                        'admin.messages.list',
                        'admin.messages',
                        'admin.messages.add'
                    ]) ? 'c-darkBlue' : 'color-black' }}"
                >{{ __('breadcrumbs.message_list') }}</a>
            </div>
            <div class="p-r-20">
                <a
                    href="{{ route('admin.settings') }}"
                    class="f-s-21 color-black text-decoration-none {{ in_array(Route::currentRouteName(), [
                        'admin.settings',
                        'admin.change_password',
                        'admin.voting_tour.list',
                        'admin.voting_tour.create',
                        'admin.voting_tour.edit',
                        'admin.ranking',
                        'admin.committee.list',
                        'admin.committee.add',
                        'admin.committee.edit'
                    ]) ? 'c-darkBlue' : 'color-black' }}"
                >{{ __('custom.settings') }}</a>
            </div>
            <div class="p-r-20">
                <a href="{{ route('admin.logout') }}" class="f-s-21 color-black text-decoration-none">{{ __('custom.exit') }}</a>
            </div>
        @else
            <div class="p-r-20 f-s-21">{{ __('custom.user') }}</div>
            <div class="p-r-20"><span class="f-s-21">{{ config('app.name') }}</span></div>
        @endif
    </div>
</nav>
<hr class="hr-thick">
