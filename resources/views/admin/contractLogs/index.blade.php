@extends('layouts.admin')

@section('content-header', __('Contract Logs'))

@section('tools')
    <a href="{{ route('admin.contractLogs.toggleScope') }}" class="btn btn-sm btn-default{{ session('ignore-contract-log-role-scope') ? ' active' : '' }}">
        @lang('View All')
    </a>
    @permission('admin.contractLogs.excel')
    <a href="{{ route('admin.contractLogs.excel', Request::query()) }}" type="submit" class="btn btn-sm btn-info">
        <i class="fa fa-file-excel-o"></i>
        @lang('Export to Excel')
    </a>
    @endpermission
    @permission('admin.contractLogs.create')
        <a href="{{ route('admin.contractLogs.create') }}" class="btn btn-sm btn-success">
            <i class="fa fa-plus"></i>
            @lang('New')
        </a>
    @endpermission
@endsection

@section('content')
<div class="contractLogs">
    <form class="box-body">
        <div class="flexboxgrid">
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <input type="text" class="form-control" name="provider" value="{{ Request::input('provider') }}" placeholder="@lang('Provider')" />
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="divisions[]" data-placeholder="@lang('Alliance OU Divisions')" multiple>
                        @foreach ($divisions as $division)
                            <option value="{{ $division->id }}" {{ in_array($division->id, Request::input('divisions') ?: []) ? 'selected' : '' }}>{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="practices[]" data-placeholder="@lang('Service Lines')" multiple>
                        @foreach ($practiceTypes as $practice)
                            <option value="{{ $practice }}" {{ in_array($practice, Request::input('practices') ?: []) ? 'selected' : '' }}>{{ $practice }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="positions[]" data-placeholder="@lang('Positions')" multiple>
                        @foreach ($positions as $position)
                            <option value="{{ $position->id }}" {{ in_array($position->id, Request::input('positions') ?: []) ? 'selected' : '' }}>{{ $position->position }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="statuses[]" data-placeholder="@lang('Statuses')" multiple>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}" {{ in_array($status->id, Request::input('statuses') ?: []) ? 'selected' : '' }}>{{ $status->contractStatus }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="accounts[]" data-placeholder="@lang('Hospital Names')" multiple>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" {{ in_array($account->id, Request::input('accounts') ?: []) ? 'selected' : '' }}>{{ $account->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="regions[]" data-placeholder="@lang('Operating Unit')" multiple>
                        @foreach ($regions as $region)
                            <option value="{{ $region->id }}" {{ in_array($region->id, Request::input('regions') ?: []) ? 'selected' : '' }}>{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="RSCs[]" data-placeholder="@lang('Regional Support Center')" multiple>
                        @foreach ($RSCs as $RSC)
                            <option value="{{ $RSC->id }}" {{ in_array($RSC->id, Request::input('RSCs') ?: []) ? 'selected' : '' }}>{{ $RSC->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <div class="input-group date">
                        <input type="text" class="form-control rangedatepicker" name="contractOutDate" value="{{ Request::input('contractOutDate') }}" placeholder="@lang('Contract Out Date')" />
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <div class="input-group date">
                        <input type="text" class="form-control rangedatepicker" name="contractInDate" value="{{ Request::input('contractInDate') ? Request::input('contractInDate') : '' }}" placeholder="@lang('Contract In Date')" />
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="recruiters[]" data-placeholder="@lang('Recruiter')" multiple>
                        @foreach ($employees as $recruiter)
                            <option value="{{ $recruiter->id }}" {{ in_array($recruiter->id, Request::input('recruiters') ?: []) ? 'selected' : '' }}>{{ $recruiter->fullName() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        
            <div class="row">
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-sm btn-info">
                        <i class="fa fa-filter"></i>
                        @lang('Apply')
                    </button>
                    <a href="{{ route('admin.contractLogs.index') }}" type="submit" class="btn btn-sm btn-default">
                        <i class="fa fa-times"></i>
                        @lang('Clear')
                    </a>
                </div>
            </div>
        </div>
    </form>

    <div class="table-responsive mh400">
        <table class="table table-hover table-bordered table-sortable">
            <thead>
                <tr>
                    <th class="mw110">@lang('Actions')</th>
                    <th class="mw90">{{ sort_column_link('provider_last_name', __('Provider Last Name')) }}</th>
                    <th class="mw90">{{ sort_column_link('provider_first_name', __('Provider First Name')) }}</th>
                    <th class="mw60">{{ sort_column_link('value', __('Value')) }}</th>
                    <th class="mw140">{{ sort_column_link('status', __('Status')) }}</th>
                    <th class="mw80">{{ sort_column_link('position', __('Position')) }}</th>
                    <th class="mw60">{{ sort_column_link('hours', __('Hours')) }}</th>
                    <th class="mw100">{{ sort_column_link('practice', __('Service Line')) }}</th>
                    <th class="mw200">{{ sort_column_link('hospital_name', __('Hospital Name')) }}</th>
                    <th class="mw80">{{ sort_column_link('site_code', __('Site Code')) }}</th>
                    <th class="mw100">{{ sort_column_link('group', __('Group')) }}</th>
                    <th class="mw110">{{ sort_column_link('division', __('Alliance OU Division')) }}</th>
                    <th class="mw100">{{ sort_column_link('contract_out', __('Contract Out')) }}</th>
                    <th class="mw100">{{ sort_column_link('contract_in', __('Contract In')) }}</th>
                    <th class="mw100">{{ sort_column_link('projected_start_date', __('Projected Start Date')) }}</th>
                    <th class="mw200">{{ sort_column_link('reason', __('Reason')) }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contractLogs as $contractLog)
                    <tr>
                        <td class="text-center">
                            @permission('admin.contractLogs.edit')
                                <a href="{{ route('admin.contractLogs.edit', [$contractLog]) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endpermission

                            @permission('admin.contractLogs.destroy')
                                <a 
                                    href="javascript:;"
                                    class="btn btn-xs btn-danger deletes-record"
                                    data-action="{{ route('admin.contractLogs.destroy', [$contractLog]) }}"
                                    data-record="{{ $contractLog->id }}"
                                    data-name="{{ $contractLog->id }}"
                                >
                                    <i class="fa fa-trash"></i>
                                </a>
                            @endpermission

                            @permission('admin.contractLogs.create')
                                <a href="{{ route('admin.contractLogs.create', ['id' => $contractLog->id]) }}" class="btn btn-xs btn-default">
                                    @lang('Amend')
                                </a>
                            @endpermission
                        </td>
                        <td>{{ $contractLog->providerLastName }}</td>
                        <td>{{ $contractLog->providerFirstName }}</td>
                        <td>{{ $contractLog->value }}</td>
                        <td>{{ $contractLog->status ? $contractLog->status->contractStatus : '' }}</td>
                        <td>{{ $contractLog->position ? $contractLog->position->position : '' }}</td>
                        <td>{{ $contractLog->numOfHours }}</td>
                        <td>{{ $contractLog->practice ? $contractLog->practice->name : '' }}</td>
                        <td>{{ $contractLog->account ? $contractLog->account->name : '' }}</td>
                        <td>{{ $contractLog->account ? $contractLog->account->siteCode : '' }}</td>
                        <td>{{ ($contractLog->division && $contractLog->division->group) ? $contractLog->division->group->name : '' }}</td>
                        <td>{{ $contractLog->division ? $contractLog->division->name : '' }}</td>
                        <td>{{ $contractLog->contractOutDate ? $contractLog->contractOutDate->format('Y-m-d') : '' }}</td>
                        <td>{{ $contractLog->contractInDate ? $contractLog->contractInDate->format('Y-m-d') : '' }}</td>
                        <td>{{ $contractLog->projectedStartDate ? $contractLog->projectedStartDate->format('Y-m-d') : '' }}</td>
                        <td>{{ $contractLog->note ? $contractLog->note->contractNote : '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if ($contractLogs->total() > 0 && $contractLogs->count() <= 0)
        <div class="well well-sm">
            @lang('No data available in this page')
        </div>
    @endif

    @if ($contractLogs->total() <= 0)
        <div class="well well-sm">
            @lang('No data available in table')
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            @lang('Showing')
            {{ $contractLogs->firstItem() }}
            @lang('to')
            {{ $contractLogs->lastItem() }}
            @lang('of')
            {{ $contractLogs->total() }} @lang('entries')
        </div>
        <div class="col-md-6 text-right">
            {{ $contractLogs->appends(Request::except('page'))->links() }}
        </div>
    </div>
</div>
@endsection
