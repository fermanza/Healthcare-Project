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
                    <div class="input-group date datepicker">
	                    <input type="text" class="form-control" id="startDate" name="startDate" value="{{ Request::input('startDate') ? Request::input('startDate') : ''}}" />
	                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                	</div>
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
		<div class="table-responsive">
	        <table id="datatable-summary" class="table table-hover table-bordered datatable">
	            <thead>
                    <tr>
                        <th colspan="16" class="white-bg">WEST RSC RECRUITING SUMMARY</th>
                        <th colspan="3" class="complete-staff-bg">COMPLETE STAFF</th>
                        <th colspan="3" class="current-staff-bg">CURRENT STAFF</th>
                        <th colspan="5" class="current-staff-bg">CURRENT OPENINGS</th>
                        <th colspan="3" class="current-staff-bg">PERCENT RECRUITED</th>
                        <th colspan="7" class="current-staff-bg">PHYSICIAN INCREASE COMP & SHIFTS BY RESOURCE TYPE</th>
                    </tr>
	                <tr>
	                    <th class="mw50">#</th>
	                    <th class="mw300 w100">@lang('Contract Name')</th>
	                    <th class="mw150 w100">@lang('Service Line')</th>
	                    <th class="mw200 w100">@lang('System Affiliation')</th>
	                    <th class="mw50 w100">@lang('JV')</th>
	                    <th class="mw200 w100">@lang('Operating Unit')</th>
	                    <th class="mw50">@lang('RSC')</th>
	                    <th class="mw150">@lang('Recruiter')</th>
	                    <th class="mw250">@lang('Secondary Recruiter')</th>
	                    <th class="mw150">@lang('Managers')</th>
	                    <th class="mw150">@lang('DOO/SVP')</th>
	                    <th class="mw150">@lang('RMD')</th>
	                    <th class="mw100">@lang('City')</th>
	                    <th class="mw100">@lang('Location')</th>
	                    <th class="mw100">@lang('Start Date')</th>
	                    <th class="mw150 w100">@lang('# of Months Account Open')</th>
                        <th class="mw50">@lang('Phys')</th>
                        <th class="mw50">@lang('APP')</th>
                        <th class="mw50">@lang('Total')</th>
                        <th class="mw50">@lang('Phys')</th>
                        <th class="mw50">@lang('APP')</th>
                        <th class="mw50">@lang('Total')</th>
                        <th class="mw50">@lang('SMD')</th>
                        <th class="mw50">@lang('AMD')</th>
                        <th class="mw50">@lang('Phys')</th>
                        <th class="mw50">@lang('APP')</th>
                        <th class="mw50">@lang('Total')</th>
                        <th class="mw100">@lang('% Recruited')</th>
                        <th class="mw150">@lang('% Recruited - Phys')</th>
                        <th class="mw150">@lang('% Recruited - APP')</th>
                        <th class="mw150">@lang('Total Physician Shifts')</th>
                        <th class="mw100">@lang('Phys Increase Comp - $')</th>
                        <th class="mw100">@lang('INC per Phys Shift - $')</th>
                        <th class="mw150">@lang('Fulltime/Cross Cred Utilization - %')</th>
                        <th class="mw150">@lang('Phys Embassador Utilization - %')</th>
                        <th class="mw150">@lang('Phys Qualitas/Tiva Utilization - %')</th>
                        <th class="mw150">@lang('Phys External Locum Utilization - %')</th>
	                </tr>
	            </thead>
	            <tbody>
	                @foreach($accounts as $account)
	                    <tr data-name="{{ $account->name }}" data-site-code="{{ $account->siteCode }}"
	                    >
	                        <td class="wd50">{{ $account->siteCode }}</td>
	                        <td class="wd300">{{ $account->{'Hospital Name'} }}</td>
	                        <td class="wd150">{{ $account->Practice }}</td>
	                        <td class="wd200">{{ $account->{'System Affiliation'} }}</td>
	                        <td class="wd50">{{-- {{ ($account->division && $account->division->isJV) ? __('Yes') : __('No') }} --}}</td>
	                        <td class="wd200">{{ $account->{'Operating Unit'} }}</td>
	                        <td class="wd50">{{-- {{ $account->rsc ? $account->rsc->name : '' }} --}}</td>
	                        <td class="wd150">{{ $account->{'RSC Recruiter'} }}</td>
	                        <td class="wd250">
	                        	{{ $account->{'Secondary Recruiter'} }}
	                        </td>
	                        <td class="wd150">{{ $account->managers }}</td>
	                        <td class="wd150">{{ $account->{'DOO/SVP'} }}</td>
	                        <td class="wd150">{{ $account->RMD }}</td>
	                        <td class="wd100">{{ $account->City }}</td>
	                        <td class="wd100">{{ $account->Location }}</td>
	                        <td class="wd100">{{ $account->{'Start Date'} ? $account->{'Start Date'}->format('d/m/y') : '' }}</td>
	                        <td class="wd150 {{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}}">
	                        	{{ $account->getMonthsSinceCreated() === INF ? '' : $account->getMonthsSinceCreated() }}
	                        </td>
                            <td class="wd50">{{ $account->{'Complete Staff - Phys'} }}</td>
                            <td class="wd50">{{ $account->{'Complete Staff - APP'} }}</td>
                            <td class="wd50">{{ $account->{'Complete Staff - Total'} }}</td>
                            <td class="wd50">{{ $account->{'Current Staff - Phys'} }}</td>
                            <td class="wd50">{{ $account->{'Current Staff - APP'} }}</td>
                            <td class="wd50">{{ $account->{'Current Staff - Total'} }}</td>
                            <td class="wd50">{{ $account->{'Current Openings - SMD'} }}</td>
                            <td class="wd50">{{ $account->{'Current Openings - AMD'} }}</td>
                            <td class="wd50">{{ $account->{'Current Openings - Phys'} }}</td>
                            <td class="wd50">{{ $account->{'Current Openings - APP'} }}</td>
                            <td class="wd50">{{ $account->{'Current Openings - Total'} }}</td>
                            <td class="wd100">{{ $account->{'Percent Recruited - Phys'} }}</td>
                            <td class="wd150">{{ $account->{'Percent Recruited - APP'} }}</td>
                            <td class="wd150">{{ $account->{'Percent Recruited - Total'} }}</td>
                            <td class="wd150">{{ $account->{'Hours - Phys'} }}</td>
                            <td class="wd100"></td>
                            <td class="wd100"></td>
                            <td class="wd150"></td>
                            <td class="wd150"></td>
                            <td class="wd150"></td>
                            <td class="wd150"></td>
	                    </tr>
	                @endforeach
	            </tbody>
	        </table>
	    </div>
	</div>
@endsection

