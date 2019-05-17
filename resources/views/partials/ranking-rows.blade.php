@foreach ($listData as $organisation)
    @php
        $class = '';
        if (isset($organisation->for_ballotage) && $organisation->for_ballotage) {
            $class = 'for-ballotage ';
        } elseif (isset($organisation->dropped_out) && $organisation->dropped_out) {
            $class = 'dropped-out ';
        }

    @endphp
    <tr class="{{ $class }}">
        <td class="text-right">{{ ++$counter }}</td>
        <td class="text-left">
            @if (!isset($orgNotEditable) || (isset($orgNotEditable) && !$orgNotEditable))
                <img
                    src="{{ asset('img/view.svg') }}"
                    class="additional-info c-pointer p-r-5"
                    data-org-additional-id="{{ $organisation->id }}"
                    height="20px"
                    width="30px"
                    title="{{ __('custom.view') }}"
                    data-toggle="tooltip"
                    data-placement="top"
                />
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
