<div class="row">
    <div class="p-l-25">
        <div class="p-l-40">
            <h3 class="p-b-15"><b>{{ isset($listTitle) ? $listTitle : '' }}</b></h3>
        </div>
        @if (!empty($stats))
            @for ($i = 0; $i < $votingCount; $i++)
                @if (isset($stats->$i) && !empty($stats->$i))
                    <div class="p-l-40">
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
<div class="row">
    <div class="{{ isset($fullWidth) && $fullWidth ? 'col-lg-12' : 'col-lg-6' }} p-l-25">
        <div class="p-l-40">
            @if (!empty($errors) && $errors->has('message'))
                @include('components.errors')
            @elseif ($listData->isEmpty())
                <div>{{ __('custom.no_info') }}</div>
            @endif
            @if ($listData->isNotEmpty())
                @php
                    $tour['id'] = request()->segment(1) == 'admin' ? $tourId : '';
                @endphp
                <form method="get" action="{{ route($route, $tour['id']) }}" id="orgList">
                    <div class="col-lg-12 text-right p-r-none p-b-15 {{ request()->segment(1) == 'admin' ? 'w-75' : 'w-100' }}">
                        <button
                            class="btn btn-primary add"
                            type="submit"
                            name="download"
                        >{{ uctrans('custom.download') }}</button>
                    </div>
                </form>
                <div class="table-wrapper nano public-table {{ request()->segment(1) == 'admin' ? 'w-75' : 'w-100' }} rank-scroll" data-vote-count="{{$votingCount}}">
                    <div class="tableFixHead nano-content js-org-table">
                        <table
                            class="table table-striped table-responsive ams-table ranking js-orgs"
                            data-ajax-url="{{ isset($ajaxMethod) ? $ajaxMethod : '' }}"
                        >
                            <thead>
                                <tr>
                                    <th class="w-5">{{ __('custom.number') }}</th>
                                    <th class="{{ ($votingCount > 0) ? 'w-55' : 'w-75' }}">{{ __('custom.organisation') }}</th>
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
                                        <th colspan="4"></th>
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
