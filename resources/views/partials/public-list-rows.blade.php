
@foreach ($listData as $singleOrg)
    <tr>
        <td class="text-right">{{$singleOrg->consecNum}}</td>
        <td class="text-left">
            <img
                src="{{ asset('img/view.svg') }}"
                class="additional-info c-pointer p-r-5"
                data-org-additional-id="{{$singleOrg->id}}"
                height="20px"
                width="30px"
                title="Преглед"
                data-toggle="tooltip"
                data-placement="top"
            />{{$singleOrg->name}}</td>
        <td class="text-center">
            @if ($singleOrg->is_candidate)
                <img src="{{ asset('img/tick.svg') }}" height="20px" width="30px" />
            @endif
        </td>
        <td>{{$singleOrg->eik}}</td>
        <td class="text-center">{{ \Carbon\Carbon::parse($singleOrg->created_at)->format('Y-m-d') }}</td>
    </tr>
@endforeach
