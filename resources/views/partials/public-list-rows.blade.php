@php
    $showCandidateCol = session()->get('ajaxMethod') == 'candidatesAjax' ? true : false;
@endphp

@foreach ($listData as $organisation)
    <tr>
        <td class="text-right">{{ ++$counter }}</td>
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
        <td class="text-left">{{ $organisation->name }}</td>
        @if ($showCandidateCol)
            <td class="text-center">
                @if ($organisation->is_candidate)
                    <img src="{{ asset('img/tick.svg') }}" height="20px" width="30px" />
                @endif
            </td>
        @endif
        <td>{{ $organisation->eik }}</td>
        <td class="text-center">{{ date('Y-m-d', strtotime($organisation->created_at)) }}</td>
    </tr>
@endforeach
@php http2_push_image('/img/view.svg'); @endphp
