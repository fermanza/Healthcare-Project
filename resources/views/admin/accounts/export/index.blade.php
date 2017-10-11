@extends('layouts.admin')

@section('content-header', __('Account Bulk Export'))

@section('tools')
    @permission('admin.accounts.export.pdf')
        <div id="bulkExport" class="btn btn-sm btn-info">
            <i class="fa fa-file-pdf-o"></i>
            @lang('Export')
        </div>
    @endpermission
@endsection

@section('content')
    <span>Filter accounts to export below</span>
    <form class="box-body">
        <div class="flexboxgrid">
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="affiliations[]" data-placeholder="@lang('Affiliation')" multiple>
                        @foreach ($affiliations as $affiliation)
                            <option value="{{ $affiliation->id }}" {{ in_array($affiliation->id, Request::input('affiliations') ?: []) ? 'selected' : '' }}>{{ $affiliation->name }}</option>
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

                <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 mb5">
                    <select class="form-control select2" name="DOO[]" data-placeholder="@lang('DOO')" multiple>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" {{ in_array($employee->id, Request::input('DOO') ?: []) ? 'selected' : '' }}>{{ $employee->fullName() }}</option>
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
                    <a href="{{ route('admin.accounts.export') }}" type="submit" class="btn btn-sm btn-default">
                        <i class="fa fa-times"></i>
                        @lang('Clear')
                    </a>
                </div>
            </div>
        </div>
    </form>
    <form action="{{route('admin.accounts.export.pdf')}}" method="POST" id="bulkExportForm">
        {{ csrf_field() }}
        <div class="table-responsive mh400">
            <table class="table table-hover table-bordered summary-datatable iscroll">
                <thead>
                    <tr>
                        <th class="mw200 w100">@lang('Name')</th>
                        <th class="mw60">@lang('Site Code')</th>
                        <th class="mw110">@lang('City')</th>
                        <th class="mw70">@lang('State')</th>
                        <th class="mw70">@lang('Start Date')</th>
                        <th class="mw70">@lang('End Date')</th>
                        <th class="mw60">@lang('Parent Site Code')</th>
                        <th class="mw40">@lang('RSC')</th>
                        <th class="mw100">@lang('Operating Unit')</th>
                        <th class="mw100">@lang('Recruiter')</th>
                        <th class="mw110">@lang('Manager')</th>
                        <th class="mw110">@lang('Credentialer')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($accounts as $account)
                        @if(!$account->parentSiteCode)
                        <tr class="{{ $account->hasEnded() ? 'danger' : ($account->isRecentlyCreated() ? 'success' : '') }}"
                            data-id="{{ $account->id }}" data-name="{{ $account->name }}" data-site-code="{{ $account->siteCode }}"
                            data-edit="{{ route('admin.accounts.edit', [$account]) }}"
                        >
                            <td>
                                {{ $account->name }}
                                <input type="hidden" name="ids[]" value="{{ $account->id }}">
                            </td>
                            <td>{{ $account->siteCode }}</td>
                            <td>{{ $account->city }}</td>
                            <td>{{ $account->state }}</td>
                            <td>{{ $account->startDate ? $account->startDate->format('m/d/Y') : '' }}</td>
                            <td>{{ $account->endDate ? $account->endDate->format('m/d/Y') : '' }}</td>
                            <td>
                                {{ $account->parentSiteCode }}
                                @permission('admin.accounts.removeParent')
                                    @if ($account->parentSiteCode)
                                        <a href="javascript:;" 
                                            class="pull-right text-danger removes-parent"
                                            data-action="{{ route('admin.accounts.removeParent', [$account]) }}"
                                            data-name="{{ $account->name }}"
                                        >
                                            <i class="fa fa-times"></i>
                                        </a>
                                    @endif
                                @endpermission
                            </td>
                            <td>{{ $account->rsc ? $account->rsc->name : '' }}</td>
                            <td>{{ $account->region ? $account->region->name : '' }}</td>
                            <td>{{ $account->recruiter ? $account->recruiter->fullName() : '' }}</td>
                            <td>{{ $account->manager ? $account->manager->fullName() : '' }}</td>
                            <td>{{ $account->credentialer ? $account->credentialer->fullName() : '' }}</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#bulkExport').on('click', function() {
                $('#bulkExportForm').submit();
            });
        });
    </script>
@endpush