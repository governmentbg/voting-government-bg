<div class="row">
    <div class="p-l-25">
        <div class="p-l-40">
            <h3 class="p-b-15"><b>{{ isset($listTitle) ? $listTitle : '' }}</b></h3>
        </div>
        @if (isset($stats['voting']) && !empty($stats['voting']))
        <div class="p-l-40">
            <h3 class="p-b-15">
                {{ __('custom.voter_turnout') .': '.
                $stats['voting']['percent'] . __('custom.percent') .' ('. $stats['voting']['voted'] .' / '. $stats['voting']['all'] .')' }}
            </h3>
        </div>
        @endif
        @if (isset($showBallotage) && $showBallotage && !empty($stats['ballotage']))
        <div class="p-l-40">
            <h3 class="p-b-15">
                {{ __('custom.voter_turnout_ballotage') .': '.
                $stats['ballotage']['percent'] . __('custom.percent') .' ('. $stats['ballotage']['voted'] .' / '. $stats['ballotage']['all'] .')' }}
            </h3>
        </div>
        @endif
    </div>
</div>
<div class="row h-600">
    <div class="{{ isset($fullWidth) && $fullWidth ? 'col-lg-12' : 'col-lg-7' }} p-l-25">
        <div class="p-l-40">
            @if (!empty($errors) && ($errors->has('message')))
                @include('components.errors')
            @elseif (empty($listData))
                <div>{{ __('custom.no_info') }}</div>
            @endif
            @if (!empty($listData))
            <div class="table-wrapper nano h-600 public-table">
                <div class="tableFixHead nano-content js-org-table">
                    <table class="table table-striped ams-table ranking js-orgs" data-ajax-url="{{isset($ajaxMethod) ? $ajaxMethod : ''}}">
                        <thead>
                            <tr>
                                <th class="w-5">{{ __('custom.number') }}</th>
                                <th class="{{ (isset($showBallotage) && $showBallotage) ? 'w-55' : 'w-75' }}">{{ __('custom.organisation') }}</th>
                                <th class="w-10">{{ __('custom.eik') }}</th>
                                <th class="w-10">{{ __('custom.votes') }}</th>
                                @if (isset($showBallotage) && $showBallotage)
                                    <th class="w-20">{{ __('custom.ballotage_votes') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="text-left ">
                            @include('partials.ranking-rows', ['counter' => 0])
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
@include('partials.public-org-data')
</div>
