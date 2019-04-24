<div class="row">
    <div class="p-l-25">
        <div class="p-l-40">
            <h3 class="p-b-15"><b>{{ isset($listTitle) ? $listTitle : '' }}</b></h3>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-7 p-l-25">
        <div class="p-l-40">
            @if (!empty($errors) && $errors->has('message'))
                @include('components.errors')
            @elseif (empty($listData))
                <div>{{ __('custom.no_info') }}</div>
            @endif
            @if (!empty($listData))
            <div class="table-wrapper">
                <div class="table-responsive ams-table-scrollable tableFixHead">
                    <table class="table table-striped ams-table">
                        <thead>
                            <tr>
                                <th class="w-5">{{ __('custom.number') }}</th>
                                <th class="w-55">{{ __('custom.organisation') }}</th>
                                <th class="w-5">{{ __('custom.candidate') }}</th>
                                <th class="w-10">{{ __('custom.eik') }}</th>
                                <th class="w-15">{{ __('custom.registered_at') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-left">
                        @php
                            $counter = 0;
                        @endphp
                        @foreach ($listData as $organisation)
                            <tr class="{{ request()->id == $organisation->id ? 'font-weight-bold' : '' }}">
                                <td class="text-right">{{ ++$counter }}</td>
                                <td class="text-left">
                                    <a href="{{ route($route, ['id' => $organisation->id]) }}#show" class="text-decoration-none">
                                        <img src="{{ asset('img/view.svg') }}" height="30px" width="30px" class="p-r-5"/>
                                    </a>
                                    {{ $organisation->name }}
                                </td>
                                <td class="text-center">
                                @if ($organisation->is_candidate)
                                    <img src="{{ asset('img/tick.svg') }}" height="30px" width="30px" />
                                @endif
                                </td>
                                <td>{{ $organisation->eik }}</td>
                                <td class="text-center">{{ date('Y-m-d', strtotime($organisation->created_at)) }}</td>
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
