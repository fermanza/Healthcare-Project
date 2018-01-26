@extends('layouts.admin')

@section('content-header', __('Summary Report'))

@section('tools')
    <a href="{{ route('admin.reports.summary.excel.details', Request::query()) }}" type="submit" class="btn btn-sm btn-info">
        <i class="fa fa-file-excel-o"></i>
        @lang('Detailed Export')
    </a>
    <a href="{{ route('admin.reports.summary.excel', Request::query()) }}" type="submit" class="btn btn-sm btn-info">
        <i class="fa fa-file-excel-o"></i>
        @lang('Export to Excel')
    </a>
    <a href="{{ route('admin.summaryReport.toggleScope') }}" class="btn btn-sm btn-success{{ session('ignore-summary-role-scope') ? ' active' : '' }}">
        @lang('View All')
    </a>
@endsection

@section('content')
    <button class="btn btn-primary mb10" data-toggle="collapse" data-target="#reportFilters">
        Show Filters
    </button>
    <p class="float-right bold">Green indicates new start</p>
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
                        @foreach ($doos as $doo)
                            <option value="{{ $doo->fullName() }}" {{ in_array($doo->fullName(), Request::input('DOO') ?: []) ? 'selected' : '' }}>{{ $doo->fullName() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="SVP[]" data-placeholder="@lang('SVP')" multiple>
                        @foreach ($SVPs as $SVP)
                            <option value="{{ $SVP->SVP }}" {{ in_array($SVP->SVP, Request::input('SVP') ?: []) ? 'selected' : '' }}>{{ $SVP->SVP }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="RMD[]" data-placeholder="@lang('RMD')" multiple>
                        @foreach ($RMDs as $RMD)
                            <option value="{{ $RMD->RMD }}" {{ in_array($RMD->RMD, Request::input('RMD') ?: []) ? 'selected' : '' }}>{{ $RMD->RMD }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="cities[]" data-placeholder="@lang('City')" multiple>
                        @foreach ($cities as $city)
                            <option value="{{ $city->city }}" {{ in_array($city->city, Request::input('cities') ?: []) ? 'selected' : '' }}>
                                {{ $city->city }}
                            </option>
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

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="groups[]" data-placeholder="@lang('Group')" multiple>
                        @foreach ($groups as $group)
                            <option value="{{ $group->id }}" {{ in_array($group->id, Request::input('groups') ?: []) ? 'selected' : '' }}>{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="sites[]" data-placeholder="@lang('Site Code')" multiple>
                        @foreach ($sites as $site)
                            <option value="{{ $site->id }}" {{ in_array($site->id, Request::input('sites') ?: []) ? 'selected' : '' }}>
                                {{ $site->siteCode }} - {{ $site->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="new" data-placeholder="@lang('Same\New')">
                        <option value=""></option>
                        <option value="1" {{ Request::input('new') == 1 ? 'selected' : '' }}>New Store</option>
                        <option value="2" {{ Request::input('new') == 2 ? 'selected' : '' }}>Same Store</option>
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="termed" data-placeholder="@lang('Active\Termed')">
                        <option value=""></option>
                        <option value="1" {{ Request::input('termed') == 1 ? 'selected' : '' }}>Active</option>
                        <option value="2" {{ Request::input('termed') == 2 ? 'selected' : '' }}>Termed</option>
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
	<div class="reports-summary maxh100vh">
		<div class="table-responsive overflow-hidden">
	        <table id="datatableSummary" class="table table-hover table-bordered">
	            <thead>
                    <tr>
                        <th colspan="17" class="white-bg">RECRUITING SUMMARY</th>
                        <th colspan="3" class="complete-staff-bg">COMPLETE STAFF</th>
                        <th colspan="3" class="current-staff-bg">CURRENT STAFF</th>
                        <th colspan="5" class="current-openings-bg">CURRENT OPENINGS</th>
                        <th colspan="3" class="percent-bg">PERCENT RECRUITED</th>
                        <th colspan="5" class="prev-month-bg">PREV MONTH</th>
                        <th colspan="7" class="mtd-bg">MTD</th>
                        <th colspan="9" class="ytd-bg">YTD</th>
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
                        <th class="thwd60">@lang('Phys +/-')</th>
                        <th class="thwd60">@lang('APP +/-')</th>
                        <th class="thwd80">@lang('Applications')</th>
                        <th class="thwd80">@lang('Interviews')</th>
                        <th class="thwd80">@lang('Pending Contracts')</th>
                        <th class="thwd80">@lang('Contracts In ')</th>
                        <th class="thwd100">@lang('Signed Not Yet Started')</th>
                        <th class="thwd100">@lang('Inc Comp')</th>
                        <th class="thwd60">@lang('Attrition')</th>
                        <th class="thwd60">@lang('Phys +/-')</th>
                        <th class="thwd60">@lang('APP +/-')</th>
	                </tr>
	            </thead>
	            <tbody>
	                @foreach($accounts as $account)
	                    <tr data-name="{{ $account->name }}" data-site-code="{{ $account->siteCode }}"
	                    >
	                        <td class="wd50 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
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
	                        <td class="wd230 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
                                <span>{{ $account->{'Hospital Name'} }}</span>
                            </td>
	                        <td class="wd80 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
                                {{ $account->Practice }}
                            </td>
	                        <td class="wd110 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
                                {{ $account->{'System Affiliation'} }}
                            </td>
	                        <td class="wd50 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
                                {{ $account->JV }}
                            </td>
	                        <td class="wd200 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
                                {{ $account->{'Operating Unit'} }}
                            </td>
	                        <td class="wd50 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
                                {{ ($account->account && $account->account->rsc) ? $account->account->rsc->name : '' }}
                            </td>
	                        <td class="wd70 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
                                {{ $account->{'RSC Recruiter'} }}
                            </td>
	                        <td class="wd110 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
	                        	{{ $account->{'Secondary Recruiter'} }}
	                        </td>
	                        <td class="wd70 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
                                {{ $account->Managers }}
                            </td>
	                        <td class="wd70 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
                                {{ $account->DOO }}
                            </td>
                            <td class="wd70 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
                                {{ $account->SVP }}
                            </td>
	                        <td class="wd70 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
                                {{ $account->RMD }}
                            </td>
	                        <td class="wd60 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
                                {{ $account->City }}
                            </td>
	                        <td class="wd100 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
                                {{ $account->Location }}
                            </td>
	                        <td class="wd100 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
                                {{ $account->{'Start Date'} ? $account->{'Start Date'}->format('m/d/y') : '' }}
                            </td>
	                        <td class="wd150 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}} {{$account->account ? ($account->account->term90less() ? 'term90less' : ($account->account->term90more() ? 'term90more' : '')) : ''}}">
	                        	{{ $account->getMonthsSinceCreated() === INF ? '' : number_format($account->getMonthsSinceCreated(), 1) }}
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
                                {{ $account->present()->{'Prev - Inc Comp'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'Prev - FT Util - %'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'Prev - Embassador Util - %'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'Prev - Int Locum Util - %'} }}
                            </td>
                            <td class="wd50">
                                {{ $account->present()->{'Prev - Ext Locum Util - %'} }}
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
                            <td class="wd100">
                                {{ $account->present()->{'MTD - Phys +/-'} }}
                            </td>
                            <td class="wd100">
                                {{ $account->present()->{'MTD - APP +/-'} }}
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
                            <td class="wd100">
                                {{ $account->present()->{'YTD - Inc Comp'} }}
                            </td>
                            <td class="wd100">
                                {{ $account->present()->{'YTD - Attrition'} }}
                            </td>
                            <td class="wd100">
                                {{ $account->present()->{'YTD - Phys +/-'} }}
                            </td>
                            <td class="wd100">
                                {{ $account->present()->{'YTD - APP +/-'} }}
                            </td>
	                    </tr>
	                @endforeach
	            </tbody>
	        </table>
	    </div>
	</div>
    <p class="notes">* Percent recruited number does not reflect sites that are overstaffed</p>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            function roundStep (number, step) {
                if (step == 0) return 0;

                var factor = 1 / step;

                return (Math.round(number * factor) / factor).toFixed(1);
            }

            var summary = $('#datatableSummary').DataTable($.extend({}, defaultDTOptions, {
                scrollY:        $('.reports-summary').height() > 400 ? $('.reports-summary').height()-180 : 400,
                scrollX:        true,
                scrollCollapse: true,
                fixedColumns:   {
                    leftColumns: 2
                },
                drawCallback: function () {
                    var api = this.api();
                    var sum17 = api.column( 17, {page:'current'} ).data().sum();
                    var sum18 = api.column( 18, {page:'current'} ).data().sum(); 
                    var sum19 = api.column( 19, {page:'current'} ).data().sum(); 
                    var sum20 = api.column( 20, {page:'current'} ).data().sum(); 
                    var sum21 = api.column( 21, {page:'current'} ).data().sum(); 
                    var sum22 = api.column( 22, {page:'current'} ).data().sum(); 
                    var sum23 = api.column( 23, {page:'current'} ).data().sum(); 
                    var sum24 = api.column( 24, {page:'current'} ).data().sum();
                    var sum25 = api.column( 25, {page:'current'} ).data().sum(); 
                    var sum26 = api.column( 26, {page:'current'} ).data().sum(); 
                    var sum27 = api.column( 27, {page:'current'} ).data().sum(); 
                    var sum28 = ((sum19 - sum27) / sum19) * 100;
                    var sum29 = ((sum17 - sum25) / sum17) * 100;
                    var sum30 = ((sum18 - sum26) / sum18) * 100;
                    var sum31 = api.column( 31, {page:'current'} ).data().sum(); 
                    var sum32 = api.column( 32, {page:'current'} ).data().sum(); 
                    var sum33 = api.column( 33, {page:'current'} ).data().sum(); 
                    var sum34 = api.column( 34, {page:'current'} ).data().sum(); 
                    var sum35 = api.column( 35, {page:'current'} ).data().sum(); 
                    var sum36 = api.column( 36, {page:'current'} ).data().sum(); 
                    var sum37 = api.column( 37, {page:'current'} ).data().sum(); 
                    var sum38 = api.column( 38, {page:'current'} ).data().sum(); 
                    var sum39 = api.column( 39, {page:'current'} ).data().sum();
                    var sum40 = api.column( 40, {page:'current'} ).data().sum(); 
                    var sum41 = api.column( 41, {page:'current'} ).data().sum();
                    var sum42 = api.column( 42, {page:'current'} ).data().sum(); 
                    var sum43 = api.column( 43, {page:'current'} ).data().sum(); 
                    var sum44 = api.column( 44, {page:'current'} ).data().sum();
                    var sum45 = api.column( 45, {page:'current'} ).data().sum(); 
                    var sum46 = api.column( 46, {page:'current'} ).data().sum(); 
                    var sum47 = api.column( 47, {page:'current'} ).data().sum();
                    var sum48 = api.column( 48, {page:'current'} ).data().sum();
                    var sum49 = api.column( 49, {page:'current'} ).data().sum();
                    var sum50 = api.column( 50, {page:'current'} ).data().sum();
                    var sum51 = api.column( 51, {page:'current'} ).data().sum();
                  
                  $( api.table().body() ).append(
                    '<tr><td>Total</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>'+(sum17 > 0 ? sum17.toFixed(1) : '')+'</td><td>'+(sum18 > 0 ? sum18.toFixed(1) : '')+'</td><td>'+(sum19 > 0 ? sum19.toFixed(1) : '')+'</td><td>'+(sum20 > 0 ? sum20.toFixed(1) : '')+'</td><td>'+(sum21 > 0 ? sum21.toFixed(1) : '')+'</td><td>'+(sum22 > 0 ? sum22.toFixed(1) : '')+'</td><td>'+(sum23 > 0 ? roundStep(sum23, 0.5) : '')+'</td><td>'+(sum24 > 0 ? roundStep(sum24, 0.5) : '')+'</td><td>'+(sum25 > 0 ? roundStep(sum25, 0.5) : '')+'</td><td>'+(sum26 > 0 ? roundStep(sum26, 0.5) : '')+'</td><td>'+(sum27 > 0 ? roundStep(sum27, 0.5) : '')+'</td><td>'+(sum28 > 0 ? (sum28 > 100 ? '100%' : sum28.toFixed(1)+'%') : '')+'</td></td><td>'+(sum29 > 0 ? (sum29 > 100 ? '100%' : sum29.toFixed(1)+'%') : '')+'</td><td>'+(sum30 > 0 ? (sum30 > 100 ? '100%' : sum30.toFixed(1)+'%') : '')+'</td><td>'+(sum31 > 0 ? '$'+sum31.toFixed(2) : '')+'</td><td></td><td></td><td></td><td></td><td>'+(sum36 > 0 ? sum36.toFixed(1) : '')+'</td><td>'+(sum37 > 0 ? sum37.toFixed(1) : '')+'</td><td>'+(sum38 > 0 ? sum38.toFixed(1) : '')+'</td><td>'+(sum39 > 0 ? sum39.toFixed(1) : '')+'</td><td>'+(sum40 > 0 ? sum40.toFixed(1) : '')+'</td><td>'+(sum41 > 0 ? sum41.toFixed(1) : '')+'</td><td>'+(sum42 > 0 ? sum42.toFixed(1) : '')+'</td><td>'+(sum43 > 0 ? sum43.toFixed(1) : '')+'</td><td>'+(sum44 > 0 ? sum44.toFixed(1) : '')+'</td><td>'+(sum45 > 0 ? sum45.toFixed(1) : '')+'</td><td>'+(sum46 > 0 ? sum46.toFixed(1) : '')+'</td><td>'+(sum47 > 0 ? sum47.toFixed(1) : '')+'</td><td>'+(sum48 > 0 ? '$'+sum48.toFixed(2) : '')+'</td><td>'+(sum49 > 0 ? sum49.toFixed(1) : '')+'</td><td>'+(sum50 > 0 ? sum50.toFixed(1) : '')+'</td><td>'+(sum51 > 0 ? sum51.toFixed(1) : '')+'</td></tr>'
                  );
                }
            }));
        } );
    </script>
@endpush