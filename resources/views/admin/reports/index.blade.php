@extends('layouts.admin')

@section('content-header', __('Summary Report'))

@section('tools')
    <a href="{{ route('admin.reports.summary.excel', Request::query()) }}" type="submit" class="btn btn-sm btn-info">
        <i class="fa fa-file-excel-o"></i>
        @lang('Export to Excel')
    </a>
@endsection

@section('content')
	<form class="box-body">
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
                            <option value="{{ $date->MonthEndDate->format('m-Y') }}" {{ $date == Request::input('monthEndDate') ? 'selected' : '' }}>{{ $date->MonthEndDate->format('m-Y') }}</option>
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
	        <table id="datatable-summary" class="table table-hover table-bordered datatable">
	            <thead>
                    <tr>
                        <th colspan="16" class="white-bg">WEST RSC RECRUITING SUMMARY</th>
                        <th colspan="3" class="complete-staff-bg">COMPLETE STAFF</th>
                        <th colspan="3" class="current-staff-bg">CURRENT STAFF</th>
                        <th colspan="5" class="current-openings-bg">CURRENT OPENINGS</th>
                        <th colspan="3" class="percent-bg">PERCENT RECRUITED</th>
                        <th colspan="2" class="white-bg">DAILY STAFFING HOURS</th>
                        <th colspan="19" class="comp-shifts-bg">PHYSICIAN INCREASE COMP & SHIFTS BY RESOURCE TYPE</th>
                        <th colspan="14" class="app-increase-bg">APP INCREASE COMP & SHIFTS BY RESOURCE TYPE</th>
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
	                    <th class="thwd70">@lang('DOO/SVP')</th>
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
                        <th class="thwd110">@lang('Physician Daily Hours')</th>
                        <th class="thwd90">@lang('APP Daily Hours')</th>
                        <th class="thwd90">@lang('Total Physician Shifts')</th>
                        <th class="thwd80">@lang('Phys Increase Comp - $')</th>
                        <th class="thwd80">@lang('INC per Phys Shift - $')</th>
                        <th class="thwd100">@lang('Fulltime/Cross Cred Utilization - %')</th>
                        <th class="thwd90">@lang('Phys Embassador Utilization - %')</th>
                        <th class="thwd100">@lang('Phys Qualitas/Tiva Utilization - %')</th>
                        <th class="thwd110">@lang('Phys External Locum Utilization - %')</th>
                        <th class="thwd90">@lang('Director INC - $')</th>
                        <th class="thwd90">@lang('Fulltime INC - $')</th>
                        <th class="thwd120">@lang('Cross Credential INC - $')</th>
                        <th class="thwd100">@lang('Embassador INC - $')</th>
                        <th class="thwd110">@lang('Internal Locum INC - $')</th>
                        <th class="thwd110">@lang('External Locum INC - $')</th>
                        <th class="thwd80">@lang('Director Shifts')</th>
                        <th class="thwd80">@lang('Fulltime Shifts')</th>
                        <th class="thwd110">@lang('Cross Credential Shifts')</th>
                        <th class="thwd100">@lang('Embassador Shifts')</th>
                        <th class="thwd110">@lang('Internal Locum Shifts')</th>
                        <th class="thwd110">@lang('External Locum Shifts')</th>
                        <th class="thwd90">@lang('Total APP Shifts')</th>
                        <th class="thwd110">@lang('APP Increase Comp - $')</th>
                        <th class="thwd90">@lang('INC per Shift - $')</th>
                        <th class="thwd100">@lang('APP Qualitas/Tiva Utilization - %')</th>
                        <th class="thwd110">@lang('APP External Locum Utilization - %')</th>
                        <th class="thwd90">@lang('Fulltime INC - $')</th>
                        <th class="thwd120">@lang('Cross Credential INC - $')</th>
                        <th class="thwd100">@lang('Embassador INC - $')</th>
                        <th class="thwd110">@lang('Internal Locum INC - $')</th>
                        <th class="thwd110">@lang('External Locum INC - $')</th>
                        <th class="thwd80">@lang('Fulltime Shifts')</th>
                        <th class="thwd110">@lang('Cross Credential Shifts')</th>
                        <th class="thwd110">@lang('Internal Locum Shifts')</th>
                        <th class="thwd110">@lang('External Locum Shifts')</th>
	                </tr>
	            </thead>
	            <tbody>
	                @foreach($accounts as $account)
	                    <tr data-name="{{ $account->name }}" data-site-code="{{ $account->siteCode }}"
	                    >
	                        <td class="wd50">{{ $account->siteCode }}</td>
	                        <td class="wd230">{{ $account->{'Hospital Name'} }}</td>
	                        <td class="wd80">{{ $account->Practice }}</td>
	                        <td class="wd110">{{ $account->{'System Affiliation'} }}</td>
	                        <td class="wd50">{{ ($account->division && $account->division->isJV) ? __('Yes') : __('No') }}</td>
	                        <td class="wd200">{{ $account->{'Operating Unit'} }}</td>
	                        <td class="wd50">{{ $account->rsc ? $account->rsc->name : '' }}</td>
	                        <td class="wd70">{{ $account->{'RSC Recruiter'} }}</td>
	                        <td class="wd110">
	                        	{{ $account->{'Secondary Recruiter'} }}
	                        </td>
	                        <td class="wd70">{{ $account->Managers }}</td>
	                        <td class="wd70">{{ $account->{'DOO/SVP'} }}</td>
	                        <td class="wd70">{{ $account->RMD }}</td>
	                        <td class="wd60">{{ $account->City }}</td>
	                        <td class="wd100">{{ $account->Location }}</td>
	                        <td class="wd100">{{ $account->{'Start Date'} ? $account->{'Start Date'}->format('d/m/y') : '' }}</td>
	                        <td class="wd150 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}}">
	                        	{{ $account->getMonthsSinceCreated() === INF ? '' : $account->getMonthsSinceCreated() }}
	                        </td>
                            <td class="wd50">{{ number_format($account->{'Complete Staff - Phys'}, 1) }}</td>
                            <td class="wd50">{{ number_format($account->{'Complete Staff - APP'}, 1) }}</td>
                            <td class="wd50">{{ number_format($account->{'Complete Staff - Total'}, 1) }}</td>
                            <td class="wd50">{{ number_format($account->{'Current Staff - Phys'}, 1) }}</td>
                            <td class="wd50">{{ number_format($account->{'Current Staff - APP'}, 1) }}</td>
                            <td class="wd50">{{ number_format($account->{'Current Staff - Total'}, 1) }}</td>
                            <td class="wd50">{{ number_format($account->{'Current Openings - SMD'}, 1) }}</td>
                            <td class="wd50">{{ number_format($account->{'Current Openings - AMD'}, 1) }}</td>
                            <td class="wd50">{{ number_format($account->{'Current Openings - Phys'}, 1) }}</td>
                            <td class="wd50">{{ number_format($account->{'Current Openings - APP'}, 1) }}</td>
                            <td class="wd50">{{ number_format($account->{'Current Openings - Total'}, 1) }}</td>
                            <td class="wd100">{{ $account->{'Percent Recruited - Total'} * 100 }}%</td>
                            <td class="wd150">{{ $account->{'Percent Recruited - Phys'} * 100 }}%</td>
                            <td class="wd150">{{ $account->{'Percent Recruited - APP'} * 100 }}%</td>
                            <td class="wd150">{{ $account->{'Hours - Phys'} }}</td>
                            <td class="wd150">{{ $account->{'Hours - APP'} }}</td>
                            <td class="wd150">{{ number_format($account->{'Total Shifts - Phys'}, 1) }}</td>
                            <td class="wd100">${{ number_format($account->{'Increased Comp - Phys'}, 2) }}</td>
                            <td class="wd100">${{ number_format($account->{'Increased Comp Per Shift - Phys'}, 2) }}</td>
                            <td class="wd150">{{ $account->{'Fulltime/Cross Cred Utlization - Phys'} * 100 }}%</td>
                            <td class="wd100">{{ $account->{'Embassador Utilization - Phys'} * 100 }}%</td>
                            <td class="wd100">{{ $account->{'Qualitas/Tiva Utilization - Phys'} * 100 }}%</td>
                            <td class="wd100">{{ $account->{'External Locum Utilization - Phys'} * 100 }}%</td>
                            <td class="wd100">
                                ${{ number_format($account->{'Director Increased Comp - Phys'}, 2) }}
                            </td>
                            <td class="wd100">
                                ${{ number_format($account->{'Full Time Increased Comp - Phys'}, 2) }}
                            </td>
                            <td class="wd100">
                                ${{ number_format($account->{'Cross Credential Increased Comp - Phys'}, 2) }}
                            </td>
                            <td class="wd100">
                                ${{ number_format($account->{'Embassador Increased Comp - Phys'}, 2) }}
                            </td>
                            <td class="wd100">
                                ${{ number_format($account->{'Internal Locum Increased Comp - Phys'}, 2) }}
                            </td>
                            <td class="wd100">
                                ${{ number_format($account->{'External Locum Increased Comp - Phys'}, 2) }}
                            </td>
                            <td class="wd100">{{ $account->{'Director Shifts - Phys'} }}</td>
                            <td class="wd100">{{ $account->{'Fulltime Shifts - Phys'} }}</td>
                            <td class="wd100">{{ $account->{'Cross Credential Shifts - Phys'} }}</td>
                            <td class="wd100">{{ $account->{'Embassador Shifts - Phys'} }}</td>
                            <td class="wd100">{{ $account->{'Internal Locum Shifts - Phys'} }}</td>
                            <td class="wd100">{{ $account->{'External Locum Shifts - Phys'} }}</td>
                            <td class="wd100">
                                {{ number_format($account->{'Total Shifts - APP'}, 1) }}
                            </td>
                            <td class="wd100">
                                ${{ number_format($account->{'Increased Comp - APP'}, 2) }}
                            </td>
                            <td class="wd100">
                                ${{ number_format($account->{'Increased Comp Per Shift - APP'}, 2) }}
                            </td>
                            <td class="wd100">{{ $account->{'Qualitas/Tiva Utilization - APP'} * 100 }}%</td>
                            <td class="wd100">{{ $account->{'External Locum Utilization - APP'} * 100 }}%</td>
                            <td class="wd100">
                                ${{ number_format($account->{'Full Time Increased Comp - APP'}, 2) }}
                            </td>
                            <td class="wd100">
                                ${{ number_format($account->{'Cross Credential Increased Comp - APP'}, 2) }}
                            </td>
                            <td class="wd100">
                                ${{ number_format($account->{'Embassador Increased Comp - APP'}, 2) }}
                            </td>
                            <td class="wd100">
                                ${{ number_format($account->{'Internal Locum Increased Comp - APP'}, 2) }}
                            </td>
                            <td class="wd100">
                                ${{ number_format($account->{'External Locum Increased Comp - APP'}, 2) }}
                            </td>
                            <td class="wd100">{{ $account->{'Fulltime Shifts - APP'} }}</td>
                            <td class="wd100">{{ $account->{'Cross Credential Shifts - APP'} }}</td>
                            <td class="wd100">{{ $account->{'Internal Locum Shifts - APP'} }}</td>
                            <td class="wd100">{{ $account->{'External Locum Shifts - APP'} }}</td>
	                    </tr>
	                @endforeach
	            </tbody>
	        </table>
	    </div>
	</div>
@endsection

