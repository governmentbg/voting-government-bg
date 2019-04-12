<div class="flex-nav-items p-t-15">
    @if(!auth()->guard('backend')->check())
    <div class="p-r-15 f-s-21">{{ __('custom.user') }}</div>
    @else
    <div class="p-r-15 f-s-21">{{ auth()->guard('backend')->user()->username }}</div>
    @endif
    <div class="p-r-15"><a href="{{ route('admin.org_list') }}" class="f-s-21 color-black text-decoration-none">{{ __('custom.registered_orgs') }}</a></div>
    <div class="p-r-15"><a href="{{ route('admin.settings') }}" class="f-s-21 color-black text-decoration-none">{{ __('custom.settings') }}</a></div>
    @if(auth()->guard('backend')->check())
    <div class="p-r-15"><a href="{{ route('admin.logout') }}" class="f-s-21 color-black text-decoration-none">{{ __('custom.exit') }}</a></div>
    @endif
</div>
<hr class="hr-thick">
