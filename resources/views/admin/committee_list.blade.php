@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')

@include('components.breadcrumbs')

@include('components.status')
@include('components.errors')
<div class="row m-r-none m-l-none">
    <div class="col-lg-12">
        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="table table-striped ams-table voting-tours-list" data-toggle="table">
                    <thead>
                        <tr>
                            <th class="w-20">
                                <a
                                    class="c-white {{ app('request')->sort == 'name' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\CommitteeController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['sort' => 'name', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc', 'page' => app('request')->page]
                                            )
                                        )
                                    }}"
                                >{{ __('custom.username') }}<img src="{{ app('request')->sort == 'name' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/></a>
                            </th>
                            <th class="w-30">
                                <a
                                    class="c-white {{ app('request')->sort == 'first_name' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\CommitteeController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['sort' => 'first_name', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc', 'page' => app('request')->page]
                                            )
                                        )
                                    }}"
                                >{{ __('custom.own_name') }}<img src="{{ app('request')->sort == 'first_name' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/></a>
                            </th>
                            <th class="w-25">
                                <a
                                    class="c-white {{ app('request')->sort == 'email' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\CommitteeController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['sort' => 'email', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc', 'page' => app('request')->page]
                                            )
                                        )
                                    }}"
                                >{{ __('custom.email') }}<img src="{{ app('request')->sort == 'email' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/></a>
                            </th>
                            <th class="w-15">
                                <a
                                    class="c-white {{ app('request')->sort == 'active' ? 'sort-active' : '' }}"
                                    href="{{
                                        action(
                                            'Admin\CommitteeController@list',
                                            array_merge(
                                                array_except(app('request')->input(), ['sort', 'order', 'page']),
                                                ['sort' => 'active', 'order' => app('request')->order == 'desc' ? 'asc' : 'desc', 'page' => app('request')->page]
                                            )
                                        )
                                    }}"
                                >{{ __('custom.status') }}<img src="{{ app('request')->sort == 'active' ? app('request')->order == 'desc' ? asset('img/arrow-down.svg') : asset('img/arrow-up.svg') : '' }}"/></a>
                            </th>
                            <th class="w-10">{{ __('custom.operations') }}</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @if(!empty($users))
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->username}}</td>
                                    <td class="text-left">{{ $user->first_name }} {{ $user->last_name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->active ? __('custom.active') : __('custom.unactive') }}</td>
                                    <td>
                                        @if($user->username != config('auth.system.user'))
                                        <a href="{{ route('admin.committee.edit', ['id' => $user->id])}}"><img src="{{ asset('img/edit.svg') }}" height="30px" width="50px"/></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @else
                        <tr><td colspan="5">{{ __('custom.no_users_found') }}</tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-12">
        @if (!empty($users))
            {{ $users->appends(['sort' => app('request')->sort, 'order' => app('request')->order])->links() }}
        @endif
    </div>
    <div class="col-lg-6 col-md-12 text-right">
        <a href="{{ route('admin.committee.add') }}" class="btn btn-primary">{{ __('custom.add_new_member') }}</a>
    </div>
</div>
@endsection
