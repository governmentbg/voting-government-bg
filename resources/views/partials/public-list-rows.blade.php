
@foreach ($listData as $organisation)
    <tr>
        <td class="text-right">{{ ++$counter }}</td>
        <td class="text-left">
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
            {{ $organisation->name }}
        </td>
        <td class="text-center">
        @if ($organisation->is_candidate)
            <img src="{{ asset('img/tick.svg') }}" height="20px" width="30px" />
        @endif
        </td>
        <td>{{ $organisation->eik }}</td>
        <td class="text-center">{{ date('Y-m-d', strtotime($organisation->created_at)) }}</td>
    </tr>
@endforeach
