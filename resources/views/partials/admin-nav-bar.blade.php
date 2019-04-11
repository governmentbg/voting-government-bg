<div class="flex-nav-items p-t-15">
    @if(!auth()->guard('backend')->check())
    <div class="p-r-15"><a href="#" class="f-s-21 color-black text-decoration-none">{{ __('custom.user') }}</a></div>
    @else
    <div class="p-r-15"><a href="#" class="f-s-21 color-black text-decoration-none">{{ auth()->guard('backend')->user()->fullName }}</a></div>
    @endif
    <div class="p-r-15"><a href="{{ route('admin.settings') }}" class="f-s-21 color-black text-decoration-none">{{ __('custom.settings') }}</a></div>
    @if(auth()->guard('backend')->check())
    <div class="p-r-15"><a href="{{ route('admin.logout') }}" class="f-s-21 color-black text-decoration-none">{{ __('custom.exit') }}</a></div>
    @endif
</div>
<hr class="hr-thick">
