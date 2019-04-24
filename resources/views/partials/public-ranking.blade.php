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
<div class="row">
    <div class="{{isset($fullWidth) && $fullWidth ? 'col-lg-12' : 'col-lg-7'}} p-l-25">
        <div class="p-l-40">
            @if (!empty($errors) && ($errors->has('message') || $errors->has('stat_message')))
                @include('components.errors', ['errorKey' => 'stat_message'])
                @include('components.errors')
            @elseif (empty($listData))
                <div>{{ __('custom.no_info') }}</div>
            @endif
            @if (!empty($listData))
            <div class="table-wrapper">
                <div class="table-responsive ams-table-scrollable tableFixHead">
                    <table class="table table-striped ams-table ranking">
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
                        <tbody class="text-left">
                        @php
                            $counter = 0;
                        @endphp
                        @foreach ($listData as $organisation)
                            @php
                            $class = '';
                            if (isset($organisation->for_ballotage) && $organisation->for_ballotage) {
                                $class = 'for-ballotage ';
                            } elseif (isset($organisation->dropped_out) && $organisation->dropped_out) {
                                $class = 'dropped-out ';
                            }
                            @endphp
                            <tr class="{{ $class . (request()->id == $organisation->id ? 'font-weight-bold' : '') }}">
                                <td class="text-right">{{ ++$counter }}</td>
                                <td class="text-left">
                                    @if(!isset($orgNotEditable) || (isset($orgNotEditable) && !$orgNotEditable))
                                    <a href="{{ route($route, ['id' => $organisation->id]) }}#show" class="text-decoration-none">
                                        <img src="{{ asset('img/view.svg') }}" height="30px" width="30px" class="p-r-5"/>
                                    </a>
                                    @endif
                                    {{ $organisation->name }}
                                </td>
                                <td>{{ $organisation->eik }}</td>
                                <td class="text-right">{{ $organisation->votes }}</td>
                                @if (isset($showBallotage) && $showBallotage)
                                    <td class="text-right">{{ isset($organisation->ballotage_votes) ? $organisation->ballotage_votes : '' }}</td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
@if (isset($orgData))
    <a name="show"></a>
    @include('partials.public-org-data')
@endif
</div>
