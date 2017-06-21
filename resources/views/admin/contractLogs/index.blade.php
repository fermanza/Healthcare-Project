@extends('layouts.admin')

@section('content-header', __('Contract Logs'))

@section('tools')
    <a href="{{ route('admin.contractLogs.create') }}" class="btn btn-sm btn-success">
        <i class="fa fa-plus"></i>
        New
    </a>
@endsection

@section('content')
    <form class="box-body">
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                <select class="form-control select2" name="divisions[]" data-placeholder="@lang('Divisions')" multiple>
                    @foreach ($divisions as $division)
                        <option value="{{ $division->id }}" {{ in_array($division->id, Request::input('divisions') ?: []) ? 'selected' : '' }}>{{ $division->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                <select class="form-control select2" name="practices[]" data-placeholder="@lang('Practices')" multiple>
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
                <input type="text" class="form-control" name="hospitalName" value="{{ Request::input('hospitalName') }}" placeholder="@lang('Hospital Name')" />
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                <div class="input-group date datepicker">
                    <input type="text" class="form-control" name="contractOutDate" value="{{ Request::input('contractOutDate') }}" placeholder="@lang('Contract Out Date')" />
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                <div class="input-group date datepicker">
                    <input type="text" class="form-control" name="contractInDate" value="{{ Request::input('contractInDate') }}" placeholder="@lang('Contract In Date')" />
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
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
    </form>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th class="mw130">@lang('Actions')</th>
                    <th class="mw100">@lang('Value')</th>
                    <th class="mw100">@lang('Status')</th>
                    <th class="mw150">@lang('Provider First Name')</th>
                    <th class="mw150">@lang('Provider Last Name')</th>
                    <th class="mw100">@lang('Position')</th>
                    <th class="mw100">@lang('Hours')</th>
                    <th class="mw100">@lang('Practice')</th>
                    <th class="mw200">@lang('Hospital Name')</th>
                    <th class="mw100">@lang('Site Code')</th>
                    <th class="mw100">@lang('Group')</th>
                    <th class="mw100">@lang('Division')</th>
                    <th class="mw100">@lang('Contract Out')</th>
                    <th class="mw100">@lang('Contract In')</th>
                    <th class="mw150">@lang('Projected Start Date')</th>
                    <th class="mw150">@lang('Reason')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contractLogs as $contractLog)
                    <tr>
                        <td class="text-center">
                            <a href="{{ route('admin.contractLogs.edit', [$contractLog]) }}" class="btn btn-xs btn-primary">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a 
                                href="javascript:;"
                                class="btn btn-xs btn-danger deletes-record"
                                data-action="{{ route('admin.contractLogs.destroy', [$contractLog]) }}"
                                data-record="{{ $contractLog->id }}"
                                data-name="{{ $contractLog->id }}"
                            >
                                <i class="fa fa-trash"></i>
                            </a>
                            <a href="{{ route('admin.contractLogs.create', ['id' => $contractLog->id]) }}" class="btn btn-xs btn-default">
                                @lang('Amend')
                            </a>
                        </td>
                        <td>{{ $contractLog->value }}</td>
                        <td>{{ $contractLog->status ? $contractLog->status->contractStatus : '' }}</td>
                        <td>{{ $contractLog->providerFirstName }}</td>
                        <td>{{ $contractLog->providerLastName }}</td>
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

    <div class="text-right">
        {{ $contractLogs->appends(Request::except('page'))->links() }}
    </div>
@endsection
