@extends('layouts.admin')

@section('content-header', __('Summary Report'))

@section('tools')
    @permission('admin.reports.usage.excel')
    <a href="{{ route('admin.reports.usage.excel', Request::query()) }}" type="submit" class="btn btn-sm btn-info">
        <i class="fa fa-file-excel-o"></i>
        @lang('Export to Excel')
    </a>
    @endpermission
    <a href="{{ route('admin.summaryReport.toggleScope') }}" class="btn btn-sm btn-success{{ session('ignore-summary-role-scope') ? ' active' : '' }}">
        @lang('View All')
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
                    <select class="form-control select2" name="affiliations[]" data-placeholder="@lang('Affiliation')" multiple>
                        @foreach ($affiliations as $affiliation)
                            <option value="{{ $affiliation->name }}" {{ in_array($affiliation->name, Request::input('affiliations') ?: []) ? 'selected' : '' }}>{{ $affiliation->name }}</option>
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
                        <option value="" disabled selected></option>
                        @foreach ($dates as $date)
                            <option value="{{ $date->MonthEndDate->format('m-Y') }}" {{ $date->MonthEndDate->format('m-Y') == Request::input('monthEndDate') ? 'selected' : '' }}>
                                {{ $date->MonthEndDate->format('m-Y') }}
                            </option>
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

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="DOO[]" data-placeholder="@lang('DOO')" multiple>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->fullName() }}" {{ in_array($employee->fullName(), Request::input('DOO') ?: []) ? 'selected' : '' }}>{{ $employee->fullName() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="states[]" data-placeholder="@lang('State')" multiple>
                        @foreach ($states as $state)
                            <option value="{{ $state->abbreviation }}" {{ in_array($state->abbreviation, Request::input('states') ?: []) ? 'selected' : '' }}>{{ $state->name }}</option>
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
                    <a href="{{ route('admin.reports.usage.index') }}" type="submit" class="btn btn-sm btn-default">
                        <i class="fa fa-times"></i>
                        @lang('Clear')
                    </a>
                </div>
            </div>
        </div>
    </form>
	<div class="reports-summary maxh100vh">
		<div class="table-responsive overflow-hidden">
	        <table id="datatableSummary" class="table table-hover table-bordered">
	            <thead>
                    <tr>
                        <th colspan="10" class="white-bg">RECRUITING SUMMARY</th>
                        <th colspan="2" class="white-bg">USAGE</th>
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
                        <th class="thwd110">@lang('Last Updated By')</th>
                        <th class="thwd100">@lang('Last Updated Time')</th>

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
	                        <td class="wd230"><span>{{ $account->{'Hospital Name'} }}</span></td>
	                        <td class="wd80">{{ $account->Practice }}</td>
	                        <td class="wd110">{{ $account->{'System Affiliation'} }}</td>
	                        <td class="wd50">{{ $account->JV }}</td>
	                        <td class="wd200">{{ $account->{'Operating Unit'} }}</td>
	                        <td class="wd50">{{ ($account->account && $account->account->rsc) ? $account->account->rsc->name : '' }}</td>
	                        <td class="wd70">{{ $account->{'RSC Recruiter'} }}</td>
	                        <td class="wd110">
	                        	{{ $account->{'Secondary Recruiter'} }}
	                        </td>
	                        <td class="wd70">{{ $account->Managers }}</td>
                            <td class="wd110">
                                {{ $account->account && $account->account->pipeline && is_object($account->account->pipeline->lastUpdate()) ? $account->account->pipeline->lastUpdate()->updatedBy->name : '' }}
                            </td>
                            <td class="wd110">
                                {{ $account->account && $account->account->pipeline && is_object($account->account->pipeline->lastUpdate()) ? ($account->account->pipeline->lastUpdate()->lastUpdated ? \Carbon\Carbon::parse($account->account->pipeline->lastUpdate()->lastUpdated)->format('m/d/Y H:i:s') : '') : '' }}
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
                scrollY:        $('.reports-summary').height() > 400 ? $('.reports-summary').height()-180 : 400,
                scrollX:        true,
                scrollCollapse: true,
            }));
        } );
    </script>
@endpush