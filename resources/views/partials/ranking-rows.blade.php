@foreach ($listData as $organisation)
    @php
        if (isset($organisation->elected) && $organisation->elected) {
            $class = '';
        } elseif (isset($organisation->for_ballotage) && $organisation->for_ballotage) {
            $class = 'for-ballotage ';
        } else {
            $class = 'dropped-out ';
        }
    @endphp
    <tr class="{{ $class }}">
        <td class="text-right">{{ ++$counter }}</td>
        @if (!isset($orgNotEditable) || (isset($orgNotEditable) && !$orgNotEditable))
            <td class="text-right p-r-none-i">
                <img
                    src="{{ asset('img/view.svg') }}"
                    class="additional-info c-pointer"
                    data-org-additional-id="{{ $organisation->id }}"
                    height="20px"
                    width="30px"
                    title="{{ __('custom.view') }}"
                    data-toggle="tooltip"
                    data-placement="top"
                />
            </td>
        @endif
        <td class="text-left">{{ $organisation->name }}</td>
        <td>{{ $organisation->eik }}</td>
        @for ($i = 0; $i < $votingCount; $i++)
            <td class="text-right">{{ isset($organisation->votes->{$i}) ? $organisation->votes->{$i} : '' }}</td>
        @endfor
    </tr>
@endforeach
