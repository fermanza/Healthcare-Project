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
                            <option value="{{ $employee->id }}" {{ in_array($employee->id, Request::input('recruiters') ?: []) ? 'selected' : '' }}>{{ $employee->fullName() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="practices[]" data-placeholder="@lang('Service Line')" multiple>
                        @foreach ($practices as $practice)
                            <option value="{{ $practice->id }}" {{ in_array($practice->id, Request::input('practices') ?: []) ? 'selected' : '' }}>{{ $practice->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="managers[]" data-placeholder="@lang('Manager')" multiple>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" {{ in_array($employee->id, Request::input('managers') ?: []) ? 'selected' : '' }}>{{ $employee->fullName() }}</option>
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
                            <option value="{{ $region->id }}" {{ in_array($region->id, Request::input('regions') ?: []) ? 'selected' : '' }}>{{ $region->name }}</option>
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
	                </tr>
	            </thead>
	            <tbody>
	                @foreach($accounts as $account)
	                    <tr data-id="{{ $account->id }}" data-name="{{ $account->name }}" data-site-code="{{ $account->siteCode }}"
	                    >
	                        <td>{{ $account->siteCode }}</td>
	                        <td>{{ $account->name }}</td>
	                        <td>{{ $account->practices->count() ? $account->practices->first()->name : '' }}</td>
	                        <td></td>
	                        <td>{{ ($account->division && $account->division->isJV) ? __('Yes') : __('No') }}</td>
	                        <td>{{ $account->region ? $account->region->name : '' }}</td>
	                        <td>{{ $account->rsc ? $account->rsc->name : '' }}</td>
	                        <td>{{ $account->recruiter ? $account->recruiter->fullName() : '' }}</td>
	                        <td>
	                        	{{ $account->recruiters->count() ? $account->recruiters->map->fullName()->implode('; ') : '' }}
	                        </td>
	                        <td>{{ $account->manager ? $account->manager->fullName() : '' }}</td>
	                        <td>{{ $account->pipeline->svp }}</td>
	                        <td>{{ $account->pipeline->rmd }}</td>
	                        <td>{{ $account->city }}</td>
	                        <td>{{ $account->state }}</td>
	                        <td>{{ $account->startDate ? $account->startDate->format('m/d/Y') : '' }}</td>
	                        <td class="{{ $account->getMonthsSinceCreated() < 7 ? 'recently-created' : ''}}">
	                        	{{ $account->getMonthsSinceCreated() === INF ? '' : $account->getMonthsSinceCreated() }}
	                        </td>
	                    </tr>
	                @endforeach
	            </tbody>
	        </table>
	    </div>
	</div>
@endsection

