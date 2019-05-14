<div class="row">
    <div class="p-l-25">
        <div class="p-l-40">
            <h3 class="p-b-15"><b>{{ isset($listTitle) ? $listTitle : '' }}</b></h3>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-7 p-l-25">
        <div class="p-l-40">
            @if (!empty($errors) && $errors->has('message'))
                @include('components.errors')
            @elseif (empty($listData))
                <div>{{ __('custom.no_info') }}</div>
            @endif
            @if (!empty($listData))
            <div class="table-wrapper nano h-600 public-table">
                <div class="tableFixHead nano-content js-org-table">
                    <table class="table table-striped ams-table js-orgs" data-ajax-url="{{isset($ajaxMethod) ? $ajaxMethod : ''}}">
                        <thead>
                            <tr>
                                <th class="w-5 no-top">{{ __('custom.number') }}</th>
                                <th class="w-55 text-left no-top">{{ __('custom.organisation') }}</th>
                                <th class="w-5 no-top">{{ __('custom.candidate') }}</th>
                                <th class="w-10 no-top">{{ __('custom.eik') }}</th>
                                <th class="w-15 no-top">{{ __('custom.registered_at') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-left">
                            @include('partials.public-list-rows', ['counter' => 0])
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
@include('partials.public-org-data')
</div>
