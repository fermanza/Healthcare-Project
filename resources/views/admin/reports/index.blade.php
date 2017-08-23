@extends('layouts.admin')

@section('content-header', __('Summary Report'))

@section('tools')
    <a href="{{ route('admin.reports.summary.excel', Request::query()) }}" type="submit" class="btn btn-sm btn-info">
        <i class="fa fa-file-excel-o"></i>
        @lang('Export to Excel')
    </a>
@endsection

@section('content')
    <button class="btn btn-primary mb10" data-toggle="collapse" data-target="#reportFilters">
        Show Filters
    </button>
	<form class="box-body collapse" id="reportFilters">
        <div class="flexboxgrid">
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="divisions[]" data-placeholder="@lang('Division')" multiple>
                        @foreach ($divisions as $division)
                            <option value="{{ $division->id }}" {{ in_array($division->id, Request::input('divisions') ?: []) ? 'selected' : '' }}>{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="recruiters[]" data-placeholder="@lang('Recruiter')" multiple>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->fullName() }}" {{ in_array($employee->fullName(), Request::input('recruiters') ?: []) ? 'selected' : '' }}>{{ $employee->fullName() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="practices[]" data-placeholder="@lang('Service Line')" multiple>
                        @foreach ($practices as $practice)
                            <option value="{{ $practice->name }}" {{ in_array($practice->name, Request::input('practices') ?: []) ? 'selected' : '' }}>{{ $practice->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="managers[]" data-placeholder="@lang('Manager')" multiple>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->fullName() }}" {{ in_array($employee->fullName(), Request::input('managers') ?: []) ? 'selected' : '' }}>{{ $employee->fullName() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="monthEndDate" data-placeholder="@lang('Month End Date')">
                        <option value=""></option>
                        @foreach ($dates as $date)
                            <option value="{{ $date->MonthEndDate->format('m-Y') }}" {{ $date->MonthEndDate->format('m-Y') == Request::input('monthEndDate') ? 'selected' : '' }}>{{ $date->MonthEndDate->format('m-Y') }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="regions[]" data-placeholder="@lang('Operating Unit')" multiple>
                        @foreach ($regions as $region)
                            <option value="{{ $region->name }}" {{ in_array($region->name, Request::input('regions') ?: []) ? 'selected' : '' }}>{{ $region->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="RSCs[]" data-placeholder="@lang('RSC')" multiple>
                        @foreach ($RSCs as $RSC)
                            <option value="{{ $RSC->id }}" {{ in_array($RSC->id, Request::input('RSCs') ?: []) ? 'selected' : '' }}>{{ $RSC->name }}</option>
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
                    <a href="{{ route('admin.reports.summary.index') }}" type="submit" class="btn btn-sm btn-default">
                        <i class="fa fa-times"></i>
                        @lang('Clear')
                    </a>
                </div>
            </div>
        </div>
    </form>
	<div class="reports-summary">
		<div class="table-responsive mh400">
	        <table id="datatableSummary" class="table table-hover table-bordered">
	            <thead>
                    <tr>
                        <th colspan="17" class="white-bg">RECRUITING SUMMARY</th>
                        <th colspan="3" class="complete-staff-bg">COMPLETE STAFF</th>
                        <th colspan="3" class="current-staff-bg">CURRENT STAFF</th>
                        <th colspan="5" class="current-openings-bg">CURRENT OPENINGS</th>
                        <th colspan="3" class="percent-bg">PERCENT RECRUITED</th>
                        <th colspan="5" class="prev-month-bg">PREV MONTH</th>
                        <th colspan="5" class="mtd-bg">MTD</th>
                        <th colspan="7" class="ytd-bg">YTD</th>
                    </tr>
	                <tr>
	                    <th class="thwd40">#</th>
	                    <th class="thwd230">@lang('Contract Name')</th>
	                    <th class="thwd80 nowrap">@lang('Service Line')</th>
	                    <th class="thwd110 nowrap">@lang('System Affiliation')</th>
	                    <th class="thwd40">@lang('JV')</th>
	                    <th class="thwd90 nowrap">@lang('Operating Unit')</th>
	                    <th class="thwd50">@lang('RSC')</th>
	                    <th class="thwd70">@lang('Recruiter')</th>
	                    <th class="thwd110">@lang('Secondary Recruiter')</th>
	                    <th class="thwd70">@lang('Managers')</th>
	                    <th class="thwd70">@lang('DOO')</th>
                        <th class="thwd70">@lang('SVP')</th>
	                    <th class="thwd70">@lang('RMD')</th>
	                    <th class="thwd60">@lang('City')</th>
	                    <th class="thwd60">@lang('Location')</th>
	                    <th class="thwd70">@lang('Start Date')</th>
	                    <th class="thwd80">@lang('# of Months Account Open')</th>
                        <th class="thwd50">@lang('Phys')</th>
                        <th class="thwd50">@lang('APP')</th>
                        <th class="thwd50">@lang('Total')</th>
                        <th class="thwd50">@lang('Phys')</th>
                        <th class="thwd50">@lang('APP')</th>
                        <th class="thwd50">@lang('Total')</th>
                        <th class="thwd50">@lang('SMD')</th>
                        <th class="thwd50">@lang('AMD')</th>
                        <th class="thwd50">@lang('Phys')</th>
                        <th class="thwd50">@lang('APP')</th>
                        <th class="thwd50">@lang('Total')</th>
                        <th class="thwd80">@lang('% Recruited')</th>
                        <th class="thwd100">@lang('% Recruited - Phys')</th>
                        <th class="thwd100">@lang('% Recruited - APP')</th>
                        <th class="thwd50">@lang('Inc Comp')</th>
                        <th class="thwd100">@lang('FT Utilization - %')</th>
                        <th class="thwd100">@lang('Embassador Utilization - %')</th>
                        <th class="thwd100">@lang('Internal Locum Utilization - %')</th>
                        <th class="thwd100">@lang('External Locum Utilization - %')</th>
                        <th class="thwd80">@lang('Applications')</th>
                        <th class="thwd80">@lang('Interviews')</th>
                        <th class="thwd80">@lang('Contracts Out')</th>
                        <th class="thwd80">@lang('Contracts in')</th>
                        <th class="thwd100">@lang('Signed Not Yet Started')</th>
                        <th class="thwd80">@lang('Applications')</th>
                        <th class="thwd80">@lang('Interviews')</th>
                        <th class="thwd80">@lang('Pending Contracts')</th>
                        <th class="thwd80">@lang('Contracts In ')</th>
                        <th class="thwd100">@lang('Signed Not Yet Started')</th>
                        <th class="thwd50">@lang('Inc Comp')</th>
                        <th class="thwd50">@lang('Attrition')</th>
	                </tr>
	            </thead>
	            <tbody>
	                @foreach($accounts as $account)
	                    <tr data-name="{{ $account->name }}" data-site-code="{{ $account->siteCode }}"
	                    >
	                        <td class="wd50">
                                @if($account->account)
                                    <a href="{{ route('admin.accounts.pipeline.index', [$account->account]) }}">
                                        {{ $account->siteCode }}
                                    </a>
                                @else
                                    <a href="#">
                                        {{ $account->siteCode }}
                                    </a>
                                @endif
                            </td>
	                        <td class="wd230">{{ $account->{'Hospital Name'} }}</td>
	                        <td class="wd80">{{ $account->Practice }}</td>
	                        <td class="wd110">{{ $account->{'System Affiliation'} }}</td>
	                        <td class="wd50">{{ ($account->account && $account->account->division && $account->account->division->isJV) ? __('Yes') : __('No') }}</td>
	                        <td class="wd200">{{ $account->{'Operating Unit'} }}</td>
	                        <td class="wd50">{{ ($account->account && $account->account->rsc) ? $account->account->rsc->name : '' }}</td>
	                        <td class="wd70">{{ $account->{'RSC Recruiter'} }}</td>
	                        <td class="wd110">
	                        	{{ $account->{'Secondary Recruiter'} }}
	                        </td>
	                        <td class="wd70">{{ $account->Managers }}</td>
	                        <td class="wd70">{{ $account->DOO }}</td>
                            <td class="wd70">{{ $account->SVP }}</td>
	                        <td class="wd70">{{ $account->RMD }}</td>
	                        <td class="wd60">{{ $account->City }}</td>
	                        <td class="wd100">{{ $account->Location }}</td>
	                        <td class="wd100">{{ $account->{'Start Date'} ? $account->{'Start Date'}->format('d/m/y') : '' }}</td>
	                        <td class="wd150 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}}">
	                        	{{ $account->getMonthsSinceCreated() === INF ? '' : $account->getMonthsSinceCreated() }}
	                        </td>
                            <td class="wd50">{{ $account->present()->{'Complete Staff - Phys'} }}</td>
                            <td class="wd50">{{ $account->present()->{'Complete Staff - APP'} }}</td>
                            <td class="wd50">{{ $account->present()->{'Complete Staff - Total'} }}</td>
                            <td class="wd50">{{ $account->present()->{'Current Staff - Phys'} }}</td>
                            <td class="wd50">{{ $account->present()->{'Current Staff - APP'} }}</td>
                            <td class="wd50">{{ $account->present()->{'Current Staff - Total'} }}</td>
                            <td class="wd50">{{ $account->present()->{'Current Openings - SMD'} }}</td>
                            <td class="wd50">{{ $account->present()->{'Current Openings - AMD'} }}</td>
                            <td class="wd50">{{ $account->present()->{'Current Openings - Phys'} }}</td>
                            <td class="wd50">{{ $account->present()->{'Current Openings - APP'} }}</td>
                            <td class="wd50">{{ $account->present()->{'Current Openings - Total'} }}</td>
                            <td class="wd50">
                                {{ $account->present()->{'Percent Recruited - Total'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'Percent Recruited - Phys'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'Percent Recruited - APP'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'Prev Month - Inc Comp'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'Prev Month - FT Utilization - %'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'Prev Month - Embassador Utilization - %'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'Prev Month - Internal Locum Utilization - %'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'Prev Month - External Locum Utilization - %'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'MTD - Applications'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'MTD - Interviews'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'MTD - Contracts Out'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'MTD - Contracts In'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'MTD - Signed Not Yet Started'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'YTD - Applications'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'YTD - Interviews'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'YTD - Pending Contracts'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'YTD - Contracts In'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'YTD - Signed Not Yet Started'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'YTD - Inc Comp'} }}
                            </td>
                            <td>
                                {{ $account->present()->{'YTD - Attrition'} }}
                            </td>
	                    </tr>
	                @endforeach
	            </tbody>
	        </table>
	    </div>
	</div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            var summary = $('#datatableSummary').DataTable($.extend({}, defaultDTOptions, {
                scrollY:        "380px",
                scrollX:        true,
                scrollCollapse: true,
                fixedColumns:   {
                    leftColumns: 2
                }
            }));
        } );
    </script>
@endpush