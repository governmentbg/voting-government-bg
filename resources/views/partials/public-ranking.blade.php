<div class="row m-r-none">
    <div class="p-l-25">
        <div class="{{ isset($fullWidth) && $fullWidth ? 'p-l-15' : 'p-l-40' }}">
            <h3 class="p-b-15"><b>{{ isset($listTitle) ? $listTitle : '' }}</b></h3>
        </div>
        @if (!empty($stats))
            @for ($i = 0; $i < $votingCount; $i++)
                @if (isset($stats->$i) && !empty($stats->$i))
                    <div class="{{ isset($fullWidth) && $fullWidth ? 'p-l-15' : 'p-l-40' }}">
                        <h3 class="p-b-15">
                            {{ ($i == 0 ? __('custom.voter_turnout') : ($votingCount > 2
                                ? __('custom.voter_turnout_ballotage_n', ['index' => $i])
                                : __('custom.voter_turnout_ballotage'))) .': '.
                            $stats->{$i}->percent . __('custom.percent') .' ('. $stats->{$i}->voted .' / '. $stats->{$i}->all .')' }}
                        </h3>
                    </div>
                @endif
            @endfor
        @endif
    </div>
</div>
<div class="row m-l-none m-r-none">
    <div class="{{ isset($fullWidth) && $fullWidth ? 'col-lg-12' : 'col-lg-7' }}">
        <div class="{{ isset($fullWidth) && $fullWidth ? 'p-l-none' : 'p-l-40' }}">
            @if (!empty($errors) && $errors->has('message'))
                @include('components.errors')
            @elseif ($listData->isEmpty())
                <div>{{ __('custom.no_info') }}</div>
            @endif
            @if ($listData->isNotEmpty())
                @php
                    $tour['id'] = request()->segment(1) == 'admin' ? $tourId : '';
                    $mainColumnsCount = 4;
                    $orgWidthClass = 'w-55';
                @endphp
                <form method="get" action="{{ route($route, $tour['id']) }}" id="orgList">
                    <div class="col-lg-12 text-right p-r-none p-b-15">
                        <button
                            class="btn btn-primary add"
                            type="submit"
                            name="download"
                        >{{ uctrans('custom.download') }}</button>
                    </div>
                </form>
                <div class="table-wrapper nano public-table" data-vote-count="{{ $votingCount }}">
                    <div class="tableFixHead nano-content js-org-table">
                        <table
                            class="table table-striped table-responsive ams-table ranking js-orgs"
                            data-ajax-url="{{ isset($ajaxMethod) ? $ajaxMethod : '' }}"
                        >
                            <thead>
                                <tr>
                                    <th class="w-5">{{ __('custom.number') }}</th>
                                    @if (!isset($orgNotEditable) || (isset($orgNotEditable) && !$orgNotEditable))
                                        <th class="w-5">&nbsp;</th>
                                        @php
                                            $mainColumnsCount++;
                                            $orgWidthClass = 'w-50';
                                        @endphp
                                    @endif
                                    <th class="{{ $orgWidthClass }}">{{ __('custom.organisation') }}</th>
                                    <th class="w-10">{{ __('custom.eik') }}</th>
                                    <th class="w-5">{{ __('custom.votes') }}</th>
                                    @if ($votingCount > 1)
                                        @if ($votingCount == 2)
                                            <th class="w-25">{{ __('custom.ballotage_votes') }}</th>
                                        @else
                                            <th class="w-25" colspan="{{ $votingCount - 1 }}">{{ __('custom.ballotage_votes') }}</th>
                                        @endif
                                    @endif
                                </tr>
                                @if ($votingCount > 2)
                                    <tr class="align-top">
                                        <th colspan="{{ $mainColumnsCount }}"></th>
                                        @for ($i = 1; $i < $votingCount; $i++)
                                            <th class="text-right">{{ $i }}</th>
                                        @endfor
                                    </tr>
                                @endif
                            </thead>
                            <tbody class="text-left">
                                @include('partials.ranking-rows', ['counter' => 0, 'orgNotEditable' => $orgNotEditable])
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @include('partials.public-org-data')
</div>
