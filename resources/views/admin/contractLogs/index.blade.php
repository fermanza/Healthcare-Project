@extends('layouts.admin')

@section('content-header', __('Contract Logs'))

@section('tools')
    <a href="{{ route('admin.contractLogs.create') }}" class="btn btn-sm btn-success">
        <i class="fa fa-plus"></i>
        New
    </a>
@endsection

@section('content')
    <div class="table-responsive">
        <table class="table table-hover table-bordered datatable">
            <thead>
                <tr>
                    <th class="mw100">@lang('Actions')</th>
                    <th class="mw100">@lang('Value')</th>
                    <th class="mw100">@lang('Status')</th>
                    <th class="mw150">@lang('Provider First Name')</th>
                    <th class="mw150">@lang('Provider Last Name')</th>
                    <th class="mw100">@lang('Position')</th>
                    <th class="mw100">@lang('Hours')</th>
                    <th class="mw100">@lang('Practice')</th>
                    <th class="mw100">@lang('Hospital Name')</th>
                    <th class="mw100">@lang('Site Code')</th>
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
@endsection
