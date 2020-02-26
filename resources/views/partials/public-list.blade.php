<div class="row m-r-none">
    <div class="p-l-25">
        <div class="p-l-40">
            <h3 class="p-b-15"><b>{{ isset($listTitle) ? $listTitle : '' }}</b></h3>
        </div>
    </div>
</div>
<div class="row m-l-none m-r-none">
    <div class="col-lg-7">
        <div class="p-l-40">
            <form method="get" action="{{ route($route) }}" id="orgList">
                <div class="col-lg-12">
                    <div class="row form-group">
                        <div class="col-lg-3 inline-block text-left p-l-none p-b-none">
                            @if (!empty($listData))
                                <button
                                    class="btn btn-primary add"
                                    type="submit"
                                    name="download"
                                >{{ uctrans('custom.download') }}</button>
                            @endif
                        </div>
                        <label for="eik" class="col-form-label p-l-15 offset-lg-3 col-lg-2 text-right">{{ __('custom.eik') }}:</label>
                        <div class="col-lg-4 p-r-none">
                            <input
                                type="text"
                                name="eik"
                                placeholder="{{__('custom.search')}}"
                                value="{{ isset($eik) ? $eik : '' }}"
                                maxlength="19"
                                class="form-control js-search search-box no-outline"
                            >
                        </div>
                    </div>
                </div>
            </form>
            @if (!empty($errors) && $errors->has('message'))
                @include('components.errors')
            @elseif (empty($listData))
                <div>{{ __('custom.no_info') }}</div>
            @endif
            @if (!empty($listData))
                @php
                    $showCandidateCol = session()->get('ajaxMethod') == 'candidatesAjax' ? true : false;
                @endphp
                <div class="table-wrapper nano public-table">
                    <div class="tableFixHead nano-content js-org-table">
                        <table
                            class="table table-striped table-responsive ams-table public js-orgs"
                            data-ajax-url="{{ isset($ajaxMethod) ? $ajaxMethod : '' }}"
                        >
                            <thead>
                                <tr>
                                    <th class="w-1">{{ __('custom.number') }}</th>
                                    <th class="w-1">&nbsp;</th>
                                    <th class="w-15">{{ __('custom.organisation') }}</th>
                                    @if ($showCandidateCol) <th class="w-1">{{ __('custom.candidate') }}</th> @endif
                                    <th class="w-1">{{ __('custom.eik') }}</th>
                                    <th class="w-1">{{ __('custom.registered_at') }}</th>
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
