@if (!empty($orgData))
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
