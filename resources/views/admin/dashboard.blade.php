@extends('layouts.admin')

@section('content-header', __('Dashboard'))

@section('content')
    <div class="flexboxgrid">
        <h2 class="mt5 mb15">@lang('Welcome')</h2>

        <div class="row">

            @component('admin/dashboard-card')
                @slot('route', 'admin.users.index')
                @slot('resource', 'Users')
                @slot('icon', 'fa-user')
            @endcomponent

            @component('admin/dashboard-card')
                @slot('route', 'admin.roles.index')
                @slot('resource', 'Roles')
                @slot('icon', 'fa-users')
            @endcomponent

            @component('admin/dashboard-card')
                @slot('route', 'admin.permissions.index')
                @slot('resource', 'Permissions')
                @slot('icon', 'fa-key')
            @endcomponent

            @component('admin/dashboard-card')
                @slot('route', 'admin.accounts.index')
                @slot('resource', 'Accounts')
                @slot('icon', 'fa-hospital-o')
            @endcomponent

            @component('admin/dashboard-card')
                @slot('route', 'admin.files.index')
                @slot('resource', 'Files')
                @slot('icon', 'fa-upload')
            @endcomponent

            @component('admin/dashboard-card')
                @slot('route', 'admin.employees.index')
                @slot('resource', 'Employees')
                @slot('icon', 'fa-id-card-o')
            @endcomponent

            @component('admin/dashboard-card')
                @slot('route', 'admin.positionTypes.index')
                @slot('resource', 'Position Types')
                @slot('icon', 'fa-id-badge')
            @endcomponent

            @component('admin/dashboard-card')
                @slot('route', 'admin.people.index')
                @slot('resource', 'People')
                @slot('icon', 'fa-male')
            @endcomponent

            @component('admin/dashboard-card')
                @slot('route', 'admin.divisions.index')
                @slot('resource', 'Divisions')
                @slot('icon', 'fa-map-marker')
            @endcomponent

            @component('admin/dashboard-card')
                @slot('route', 'admin.practices.index')
                @slot('resource', 'Practices')
                @slot('icon', 'fa-tag')
            @endcomponent

            @component('admin/dashboard-card')
                @slot('route', 'admin.groups.index')
                @slot('resource', 'Groups')
                @slot('icon', 'fa-flag-o')
            @endcomponent

            @component('admin/dashboard-card')
                @slot('route', 'admin.regions.index')
                @slot('resource', 'Operating Units')
                @slot('icon', 'fa-globe')
            @endcomponent

            @component('admin/dashboard-card')
                @slot('route', 'admin.contractLogs.index')
                @slot('resource', 'Contract Logs')
                @slot('icon', 'fa-history')
            @endcomponent

        </div>
    </div>
@endsection
