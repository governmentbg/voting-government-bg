<div class="flex-nav-items p-t-15">
    <div class="p-l-25 f-s-21">
        @if (!empty($votingTourData))
            @if ($votingTourData->showTick)
                <img src="{{ asset('img/tick.svg') }}" height="30px" width="30px" class="display-inline m-t-12"/>
            @endif
            <h3 class="display-inline">{{ $votingTourData->name .' - '. $votingTourData->statusName }}</h3>
        @endif
    </div>
    <div class="display-flex">
        @if (auth()->guard('backend')->check())
            <div class="p-r-20 f-s-21">{{ auth()->guard('backend')->user()->username }}</div>
            <div class="p-r-20">
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
</div>
<hr class="hr-thick">
