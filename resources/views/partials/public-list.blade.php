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
            @if (!empty($listData))
            <div class="table-wrapper">
                <div class="table-responsive ams-table-scrollable tableFixHead">
                    <table class="table table-striped ams-table">
                        <thead>
                            <tr>
                                <th class="w-50">{{ __('custom.organisation') }}</th>
                                <th class="w-5">{{ __('custom.candidate') }}</th>
                                <th class="w-15">{{ __('custom.eik') }}</th>
                                <th class="w-30">{{ __('custom.registered_at') }}</th>
                            </tr>
                        </thead>
                        <tbody class="text-left">
                        @foreach ($listData as $organisation)
                            <tr class="{{ request()->id == $organisation->id ? 'font-weight-bold' : ''}}" >
                                <td class="text-left">
                                    <a href="{{ route($route, ['id' => $organisation->id]) }}" class="text-decoration-none">
                                        <img src="{{ asset('img/view.svg') }}" height="30px" width="30px" class="p-r-5"/>
                                    </a>
                                    {{ $organisation->name}}
                                </td>
                                <td class="text-center">
                                @if ($organisation->is_candidate)
                                    <img src="{{ asset('img/tick.svg') }}" height="30px" width="30px" />
                                @endif
                                </td>
                                <td>{{ $organisation->eik }}</td>
                                <td>{{ date('Y-m-d', strtotime($organisation->created_at)) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
@if (isset($orgData) && !empty($orgData))
    <div class="col-lg-4 p-l-40 p-t-15">
        <div class="p-t-15">
            <div class="p-t-15">
                <table class="w-100">
                    <thead>
                        <tr>
                            <th colspan="2">
                                <h4 class="p-t-15">{{ __('custom.data_for_org_name', ['org_name' => $orgData->name]) }}</h4>
                                <hr class="hr-thin ml-0 mb-2">
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="w-30">{{ __('custom.name') }}</td>
                            <td>{{ $orgData->name }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('custom.eik') }}</td>
                            <td>{{ $orgData->eik }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('custom.address') }}</td>
                            <td>{{ $orgData->address }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('custom.representative') }}</td>
                            <td>{{ $orgData->representative }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('custom.reg_date') }}</td>
                            <td>{{ date('Y-m-d', strtotime($orgData->created_at)) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
 @endif
</div>
