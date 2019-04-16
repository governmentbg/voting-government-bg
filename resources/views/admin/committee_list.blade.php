@extends('layouts.app')

@section('content')
@include('partials.admin-nav-bar')

@include('components.breadcrumbs')

@include('components.status')
@include('components.errors')
<div class="row">
    <div class="col-lg-12">
        <div class="table-wrapper">
            <div class="table-responsive">
                <table class="table table-striped ams-table voting-tours-list" data-toggle="table">
                    <thead>
                        <tr>
                            <th class="w-20" data-field="username" data-sortable="true">{{ __('custom.username') }}</th>
                            <th class="w-30" data-field="full_name" data-sortable="true">{{ __('custom.own_name') }}</th>
                            <th class="w-25" data-field="email" data-sortable="true">{{ __('custom.email') }}</th>
                            <th class="w-15" data-field="active" data-sortable="true">{{ __('custom.status') }}</th>
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
        @if(!empty($users))
        {{ $users->links() }}
        @endif
    </div>
    <div class="col-lg-6 col-md-12 text-right">
        <a href="{{ route('admin.committee.add') }}" class="btn btn-primary">{{ __('custom.add_new_member') }}</a>
    </div>
</div>
@endsection
