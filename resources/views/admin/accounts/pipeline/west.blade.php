@extends('layouts.admin')

@section('content-header', __('Summary'))

@section('tools')
    <div class="row">
        <div class="col-xs-6"></div>
        <div class="mb10 col-xs-6">
            <select class="form-control select2" id="accountId" name="accountId" required>
                <option value="" disabled selected></option>
                @foreach ($accounts as $accountItem)
                    <option value="{{ $accountItem->id }}"}>{{ $accountItem->siteCode }} - {{ $accountItem->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <a href="javascript:print();" class="btn btn-default btn-sm hidden-print">
        <i class="fa fa-print"></i>
        @lang('Print')
    </a>
    @permission('admin.accounts.pipeline.export.word')
        <a href="{{ route('admin.accounts.pipeline.export.word', [$account]) }}" type="submit" class="btn btn-sm btn-info">
            <i class="fa fa-file-word-o"></i>
            @lang('Export')
        </a>
    @endpermission
    @permission('admin.accounts.pipeline.export.excel')
        <a href="{{ route('admin.accounts.pipeline.export.excel', [$account]) }}" type="submit" class="btn btn-sm btn-info">
            <i class="fa fa-file-excel-o"></i>
            @lang('Export')
        </a>
    @endpermission
@endsection

@section('content')
    <div id="app" class="pipeline">
        <form action="{{ route('admin.accounts.pipeline.update', [$account]) }}" method="POST">
            {{ csrf_field() }}
            {{ method_field('PATCH') }}

            <div class="no-break-inside">
                <div class="row">
                    <div class="col-md-12 text-center lead">
                        {{ $account->name }}, {{ $account->siteCode }}
                        <br />
                        {{ $account->googleAddress }}
                        <br />
                        {{ $account->recruiter ? ($account->recruiter->fullName().', ') : '' }}
                        {{ $account->manager ? $account->manager->fullName() : '' }}
                    </div>
                </div>
            </div>

            <hr />

            <div class="no-break-inside">
                <div class="row">
                    <div class="mb5 col-xs-offset-1 col-xs-5 col-sm-offset-0 col-sm-2 text-right">
                        <div class="form-group{{ $errors->has('medicalDirector') ? ' has-error' : '' }}">
                            <label v-if="activeRosterPhysicians.length > 0 || !oldChief.length" for="medicalDirector">
                                @lang('Medical Director'):
                            </label>
                            <label v-if="activeRosterPhysicians.length == 0 && oldChief.length" for="medicalDirector">
                                @lang('Chief'):
                            </label>
                        </div>
                    </div>
                    <div class="mb5 col-xs-5 col-sm-2">
                        <div class="form-group{{ $errors->has('medicalDirector') ? ' has-error' : '' }}">
                            <input type="text" class="form-control hidden-print" id="medicalDirector" name="medicalDirector" v-model="pipeline.medicalDirector" value="{{ old('medicalDirector') ?: $pipeline->medicalDirector }}" />
                            <span class="visible-print">@{{ pipeline.medicalDirector }}</span>
                            @if ($errors->has('medicalDirector'))
                                <span class="help-block"><strong>{{ $errors->first('medicalDirector') }}</strong></span>
                            @endif
                        </div>
                    </div>
                    <div class="mb5 col-xs-offset-1 col-xs-5 col-sm-offset-0 col-sm-2 text-right">
                        <div class="form-group{{ $errors->has('rmd') ? ' has-error' : '' }}">
                            <label for="rmd">@lang('RMD'):</label>
                        </div>
                    </div>
                    <div class="mb5 col-xs-5 col-sm-2">
                        <div class="form-group{{ $errors->has('rmd') ? ' has-error' : '' }}">
                            <input type="text" class="form-control hidden-print" id="rmd" name="rmd" value="{{ old('rmd') ?: $pipeline->rmd }}" />
                            <span class="visible-print">@{{ pipeline.rmd }}</span>
                            @if ($errors->has('rmd'))
                                <span class="help-block"><strong>{{ $errors->first('rmd') }}</strong></span>
                            @endif
                        </div>
                    </div>
                    <div class="mb5 col-xs-offset-1 col-xs-5 col-sm-offset-0 col-sm-2 text-right">
                        <div class="form-group{{ $errors->has('rsc') ? ' has-error' : '' }}">
                            <label for="rsc">@lang('RSC'):</label>
                        </div>
                    </div>
                    <div class="mb5 col-xs-5 col-sm-2">
                        <input type="text" class="form-control hidden-print" id="rsc" name="rsc" value="{{ $account->rsc ? $account->rsc->name : '' }}" disabled />
                        <span class="visible-print">{{ $account->rsc ? $account->rsc->name : '' }}</span>
                    </div>
                </div>
                
                <div class="row">
                    <div class="mb5 col-xs-offset-1 col-xs-5 col-sm-offset-0 col-sm-2 text-right">
                        <div class="form-group{{ $errors->has('svp') ? ' has-error' : '' }}">
                            <label for="svp">@lang('SVP'):</label>
                        </div>
                    </div>
                    <div class="mb5 col-xs-5 col-sm-2">
                        <div class="form-group{{ $errors->has('svp') ? ' has-error' : '' }}">
                            <input type="text" class="form-control hidden-print" id="svp" name="svp" value="{{ old('svp') ?: $pipeline->svp }}" />
                            <span class="visible-print">@{{ pipeline.svp }}</span>
                            @if ($errors->has('svp'))
                                <span class="help-block"><strong>{{ $errors->first('svp') }}</strong></span>
                            @endif
                        </div>
                    </div>
                    <div class="mb5 col-xs-offset-1 col-xs-5 col-sm-offset-0 col-sm-2 text-right">
                        <div class="form-group{{ $errors->has('dca') ? ' has-error' : '' }}">
                            <label for="dca">@lang('DOO'):</label>
                        </div>
                    </div>
                    <div class="mb5 col-xs-5 col-sm-2">
                        <div class="form-group{{ $errors->has('dca') ? ' has-error' : '' }}">
                            <input type="text" class="form-control hidden-print" id="dca" name="dca" value="{{ old('dca') ?: $pipeline->dca }}" />
                            <span class="visible-print">@{{ pipeline.dca }}</span>
                            @if ($errors->has('dca'))
                                <span class="help-block"><strong>{{ $errors->first('dca') }}</strong></span>
                            @endif
                        </div>
                    </div>
                    <div class="mb5 col-xs-offset-1 col-xs-5 col-sm-offset-0 col-sm-2 text-right">
                        <div class="form-group{{ $errors->has('region') ? ' has-error' : '' }}">
                            <label for="region">@lang('Operating Unit'):</label>
                        </div>
                    </div>
                    <div class="mb5 col-xs-5 col-sm-2">
                        <input type="text" class="form-control hidden-print" id="region" name="region" value="{{ $region ? $region->name : '' }}" disabled />
                        <span class="visible-print">{{ $region ? $region->name : '' }}</span>
                    </div>
                </div>
                
                <div class="row">
                    <div class="mb5 col-xs-offset-1 col-xs-5 col-sm-offset-0 col-sm-2 text-right">
                        <label for="practice">@lang('Service Line'):</label>
                    </div>
                    <div class="mb5 col-xs-5 col-sm-2">
                        <input type="text" class="form-control hidden-print" id="practice" name="practice" value="{{ $practice ? $practice->name : '' }}" disabled />
                        <span class="visible-print">{{ $practice ? $practice->name : '' }}</span>
                    </div>
                    @if ($practice && $practice->isIPS())
                        <div class="mb5 col-xs-offset-1 col-xs-5 col-sm-offset-0 col-sm-2 text-right">
                            <div class="form-group{{ $errors->has('practiceTime') ? ' has-error' : '' }}">
                                <label for="practiceTime">@lang('Service Line Time'):</label>
                            </div>
                        </div>
                        <div class="mb5 col-xs-5 col-sm-2">
                            <div class="form-group{{ $errors->has('practiceTime') ? ' has-error' : '' }}">
                                <select class="form-control hidden-print" id="practiceTime" name="practiceTime" v-model="pipeline.practiceTime">
                                    {{-- <option value="" disabled selected></option> --}}
                                    @foreach ($practiceTimes as $name => $practiceTime)
                                        <option value="{{ $practiceTime }}" {{ (old('practiceTime') == $practiceTime ?: ($pipeline->practiceTime == $practiceTime)) ? 'selected': '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <span class="visible-print text-uppercase">@{{ pipeline.practiceTime }}</span>
                                @if ($errors->has('practiceTime'))
                                    <span class="help-block"><strong>{{ $errors->first('practiceTime') }}</strong></span>
                                @endif
                            </div>
                        </div>
                    @else
                        <input type="hidden" name="practiceTime" value="hours" />
                    @endif
                </div>
            </div>

            @permission('admin.accounts.pipeline.update')
                <div class="row hidden-print">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-info">
                            Update
                        </button>
                    </div>
                </div>
            @endpermission

            <hr />

            <div class="no-break-inside">
                <h4>@lang('Complete Staffing and Current Openings')</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-gray">
                            <tr>
                                <th colspan="3" class="text-center">@lang('Physician')</th>
                                <th colspan="3" class="text-center">@lang('APPs')</th>
                                <th class="text-center hidden-print">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody v-if="pipeline.practiceTime == 'hours'">
                            <tr>
                                <td class="w15">@lang('Full Time Hours')</td>
                                <td class="w15">&nbsp;</td>
                                <td class="w15">
                                    <input type="text" class="form-control hidden-print" name="fullTimeHoursPhys" value="{{ old('fullTimeHoursPhys') ?: $pipeline->fullTimeHoursPhys }}" v-model="fullTimeHoursPhys" />
                                    <span class="visible-print">@{{ fullTimeHoursPhys }}</span>
                                </td>
                                <td class="w15">@lang('Full Time Hours')</td>
                                <td class="w15">&nbsp;</td>
                                <td class="w15">
                                    <input type="text" class="form-control hidden-print" name="fullTimeHoursApps" value="{{ old('fullTimeHoursApps') ?: $pipeline->fullTimeHoursApps }}" v-model="fullTimeHoursApps" />
                                    <span class="visible-print">@{{ fullTimeHoursApps }}</span>
                                </td>
                                <td rowspan="5" class="text-center hidden-print">
                                     @permission('admin.accounts.pipeline.update')
                                        <button type="submit" class="btn btn-info">
                                            Update
                                        </button>
                                    @endpermission
                                </td>
                            </tr>
                            <tr>
                                <th>&nbsp;</th>
                                <th class="text-center">
                                    <span>
                                        @lang('Hours')
                                    </span>
                                </th>
                                <th class="text-center">
                                    <span>
                                        @lang('FTEs')
                                    </span>
                                </th>
                                <th>&nbsp;</th>
                                <th class="text-center">
                                    <span>
                                        @lang('Hours')
                                    </span>
                                </th>
                                <th class="text-center">
                                    <span>
                                        @lang('FTEs')
                                    </span>
                                </th>
                            </tr>
                            <tr>
                                <td>@lang('Haves')</td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffPhysicianHaves" value="{{ old('staffPhysicianHaves') ?: $pipeline->staffPhysicianHaves }}" v-model="staffPhysicianHaves" readonly />
                                    <span class="visible-print">@{{ staffPhysicianHaves }}</span>
                                </td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffPhysicianFTEHaves" value="{{ old('staffPhysicianFTEHaves') ?: $pipeline->staffPhysicianFTEHaves }}" v-model="staffPhysicianFTEHaves" readonly />
                                    <span class="visible-print">@{{ staffPhysicianFTEHaves }}</span>
                                </td>
                                <td>@lang('Haves')</td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffAppsHaves" value="{{ old('staffAppsHaves') ?: $pipeline->staffAppsHaves }}" v-model="staffAppsHaves" readonly />
                                    <span class="visible-print">@{{ staffAppsHaves }}</span>
                                </td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffAppsFTEHaves" value="{{ old('staffAppsFTEHaves') ?: $pipeline->staffAppsFTEHaves }}" v-model="staffAppsFTEHaves" readonly />
                                    <span class="visible-print">@{{ staffAppsFTEHaves }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('Needs')</td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffPhysicianNeeds" value="{{ old('staffPhysicianNeeds') ?: $pipeline->staffPhysicianNeeds }}" v-model="staffPhysicianNeeds" />
                                    <span class="visible-print">@{{ pipeline.staffPhysicianNeeds }}</span>
                                </td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffPhysicianFTENeeds" value="{{ old('staffPhysicianFTENeeds') ?: $pipeline->staffPhysicianFTENeeds }}" v-model="staffPhysicianFTENeeds" readonly />
                                    <span class="visible-print">@{{ staffPhysicianFTENeeds }}</span>
                                </td>
                                <td>@lang('Needs')</td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffAppsNeeds" value="{{ old('staffAppsNeeds') ?: $pipeline->staffAppsNeeds }}" v-model="staffAppsNeeds" />
                                    <span class="visible-print">@{{ pipeline.staffAppsNeeds }}</span>
                                </td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffAppsFTENeeds" value="{{ old('staffAppsFTENeeds') ?: $pipeline->staffAppsFTENeeds }}" v-model="staffAppsFTENeeds" readonly />
                                    <span class="visible-print">@{{ staffAppsFTENeeds }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('Openings')</td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffPhysicianOpenings" value="{{ old('staffPhysicianOpenings') ?: $pipeline->staffPhysicianOpenings }}" v-model="staffPhysicianOpenings" readonly />
                                    <span class="visible-print">@{{ staffPhysicianOpenings }}</span>
                                </td>
                                <td>
                                    <div class="inline-block">
                                        Phys
                                    </div>
                                    <div class="inline-block w40p mr15">
                                        <input type="text" class="form-control hidden-print text-center" name="staffPhysicianFTEOpenings" value="{{ old('staffPhysicianFTEOpenings') ?: $pipeline->staffPhysicianFTEOpenings }}" v-model="staffPhysicianFTEOpenings" readonly />
                                        <span class="visible-print">@{{ staffPhysicianOpenings }}</span>
                                    </div>
                                    <div class="inline-block">
                                        SMD
                                    </div>
                                    <div class="inline-block w40p">
                                        <input type="text" class="form-control hidden-print text-center" name="SMDOpenings" v-model="SMDOpenings" readonly />
                                        <span class="visible-print">@{{ SMDOpenings }}</span>
                                    </div>
                                </td>
                                <td>@lang('Openings')</td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffAppsOpenings" value="{{ old('staffAppsOpenings') ?: $pipeline->staffAppsOpenings }}" v-model="staffAppsOpenings" readonly />
                                    <span class="visible-print">@{{ staffAppsOpenings }}</span>
                                </td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffAppsFTEOpenings" value="{{ old('staffAppsFTEOpenings') ?: $pipeline->staffAppsFTEOpenings }}" v-model="staffAppsFTEOpenings" readonly />
                                    <span class="visible-print">@{{ staffAppsFTEOpenings }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('Percent Recruited Actual')</td>
                                <td>
                                        <input type="text" class="form-control hidden-print" name="recruitedPhys" value="{{ number_format($percentRecruitedPhys, 1) }}%" readonly />
                                </td>
                                <td>&nbsp;</td>
                                <td>@lang('Percent Recruited Actual')</td>
                                <td>
                                        <input type="text" class="form-control hidden-print" name="recruitedApp" value="{{ number_format($percentRecruitedApp, 1) }}%" readonly />
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>@lang('Percent Recruited Reported')</td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="recruitedPhys" value="{{ number_format($percentRecruitedPhysReport, 1) }}%" readonly />
                                </td>
                                <td>&nbsp;</td>
                                <td>@lang('Percent Recruited Reported')</td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="recruitedApp" value="{{ number_format($percentRecruitedAppReport, 1) }}%" readonly />
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                        <tbody v-show="pipeline.practiceTime == 'fte'" v-cloak>
                            <tr>
                                <td class="w15">@lang('Full Time Hours')</td>
                                <td class="w15">&nbsp;</td>
                                <td class="w15">
                                    <input type="text" class="form-control hidden-print" name="fullTimeHoursPhys" value="{{ old('fullTimeHoursPhys') ?: $pipeline->fullTimeHoursPhys }}" v-model="fullTimeHoursPhys" />
                                    <span class="visible-print">@{{ fullTimeHoursPhys }}</span>
                                </td>
                                <td class="w15">@lang('Full Time Hours')</td>
                                <td class="w15">&nbsp;</td>
                                <td class="w15">
                                    <input type="text" class="form-control hidden-print" name="fullTimeHoursApps" value="{{ old('fullTimeHoursApps') ?: $pipeline->fullTimeHoursApps }}" v-model="fullTimeHoursApps" />
                                    <span class="visible-print">@{{ fullTimeHoursApps }}</span>
                                </td>
                                <td rowspan="5" class="text-center hidden-print">
                                     @permission('admin.accounts.pipeline.update')
                                        <button type="submit" class="btn btn-info">
                                            Update
                                        </button>
                                    @endpermission
                                </td>
                            </tr>
                            <tr>
                                <th>&nbsp;</th>
                                <th class="text-center">
                                    <span>
                                        @lang('FTEs')
                                    </span>
                                </th>
                                <th class="text-center">&nbsp;</th>
                                <th>&nbsp;</th>
                                <th class="text-center">
                                    <span>
                                        @lang('FTEs')
                                    </span>
                                </th>
                                <th class="text-center">&nbsp;</th>
                            </tr>
                            <tr>
                                <td>@lang('Haves')</td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffPhysicianHaves" value="{{ old('staffPhysicianHaves') ?: $pipeline->staffPhysicianHaves }}" v-model="staffPhysicianHaves" readonly />
                                    <span class="visible-print">@{{ staffPhysicianHaves }}</span>
                                </td>
                                <td>&nbsp;</td>
                                <td>@lang('Haves')</td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffAppsHaves" value="{{ old('staffAppsHaves') ?: $pipeline->staffAppsHaves }}" v-model="staffAppsHaves" readonly />
                                    <span class="visible-print">@{{ staffAppsHaves }}</span>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>@lang('Needs')</td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffPhysicianNeeds" value="{{ old('staffPhysicianNeeds') ?: $pipeline->staffPhysicianNeeds }}" v-model="staffPhysicianNeeds" />
                                    <span class="visible-print">@{{ pipeline.staffPhysicianNeeds }}</span>
                                </td>
                                <td>&nbsp;</td>
                                <td>@lang('Needs')</td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffAppsNeeds" value="{{ old('staffAppsNeeds') ?: $pipeline->staffAppsNeeds }}" v-model="staffAppsNeeds" />
                                    <span class="visible-print">@{{ pipeline.staffAppsNeeds }}</span>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>@lang('Openings')</td>
                                <td>
                                    <div class="inline-block">
                                        Phys
                                    </div>
                                    <div class="inline-block w40p mr15">
                                        <input type="text" class="form-control hidden-print text-center" name="staffPhysicianOpenings" value="{{ old('staffPhysicianOpenings') ?: $pipeline->staffPhysicianOpenings }}" v-model="staffPhysicianOpenings" readonly />
                                        <span class="visible-print">@{{ staffPhysicianOpenings }}</span>
                                    </div>
                                    <div class="inline-block">
                                        SMD
                                    </div>
                                    <div class="inline-block w40p">
                                        <input type="text" class="form-control hidden-print text-center" name="SMDOpenings" v-model="SMDOpenings" readonly />
                                        <span class="visible-print">@{{ SMDOpenings }}</span>
                                    </div>
                                </td>
                                <td>&nbsp;</td>
                                <td>@lang('Openings')</td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="staffAppsOpenings" value="{{ old('staffAppsOpenings') ?: $pipeline->staffAppsOpenings }}" v-model="staffAppsOpenings" readonly />
                                    <span class="visible-print">@{{ staffAppsOpenings }}</span>
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>@lang('Percent Recruited Actual')</td>
                                <td>
                                        <input type="text" class="form-control hidden-print" name="recruitedPhys" value="{{ number_format($percentRecruitedPhys, 1) }}%" readonly />
                                </td>
                                <td>&nbsp;</td>
                                <td>@lang('Percent Recruited Actual')</td>
                                <td>
                                        <input type="text" class="form-control hidden-print" name="recruitedApp" value="{{ number_format($percentRecruitedApp, 1) }}%" readonly />
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td>@lang('Percent Recruited Reported')</td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="recruitedPhys" value="{{ number_format($percentRecruitedPhysReport, 1) }}%" readonly />
                                </td>
                                <td>&nbsp;</td>
                                <td>@lang('Percent Recruited Reported')</td>
                                <td>
                                    <input type="text" class="form-control hidden-print" name="recruitedApp" value="{{ number_format($percentRecruitedAppReport, 1) }}%" readonly />
                                </td>
                                <td>&nbsp;</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>


        <hr />


        @if (Auth::user()->hasRoleId(11))
            <div class="no-break-inside">
                <h4 class="pipeline-blue-title">@lang('Credentialing Pipeline')</h4>
                <h6 class="pseudo-header bg-gray">@lang('Physician')</h6>
                <form @submit.prevent="addCredentialing('credentialingPhysician')">
                    <div class="table-responsive">
                        <table class="table table-bordered summary-datatable">
                            <thead class="bg-gray">
                                <tr>
                                    <th class="mw60"></th>
                                    <th class="mw180">@lang('Name')</th>
                                    <th class="mw50">@lang('Hours')</th>
                                    <th class="mw70">@lang('FT/PT/EMB')</th>
                                    <th class="mw120">@lang('File To Credentialing')</th>
                                    <th class="mw100">@lang('APP To Hospital')</th>
                                    <th class="mw50">@lang('Stage')</th>
                                    <th class="mw100">@lang('Privilege Goal')</th>
                                    <th class="mw120">@lang('Enrollment Status')</th>
                                    <th class="mw150">@lang('Enrollment Notes')</th>
                                    <th class="mw150">@lang('Credentialing Notes')</th>
                                    <th class="mw100 text-center hidden-print">@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(credentialing, index) in credentialingPhysicians">
                                    <td>
                                        <input type='radio' class="icheck-red" :name="'stopLight' + index" value="red"/ v-model="credentialing.stopLight" @change="updateStopLight(credentialing, 'red')">
                                        <input type='radio' class="icheck-yellow" :name="'stopLight' + index" value="yellow"/ v-model="credentialing.stopLight" @change="updateStopLight(credentialing, 'yellow')">
                                        <input type='radio' class="icheck-green" :name="'stopLight' + index" value="green"/ v-model="credentialing.stopLight" @change="updateStopLight(credentialing, 'green')">
                                    </td>
                                    <td>@{{ credentialing.name }}</td>
                                    <td>@{{ credentialing.hours }}</td>
                                    <td class="text-uppercase">@{{ credentialing.contract }}</td>
                                    <td>@{{ moment(credentialing.fileToCredentialing) }}</td>
                                    <td>@{{ moment(credentialing.appToHospital) }}</td>
                                    <td>@{{ credentialing.stage }}</td>
                                    <td>@{{ moment(credentialing.privilegeGoal) }}</td>
                                    <td>@{{ credentialing.enrollmentStatus }}</td>
                                    <td>@{{ credentialing.enrollmentNotes }}</td>
                                    <td>@{{ credentialing.credentialingNotes }}</td>
                                    <td class="text-center hidden-print">
                                        @permission('admin.accounts.pipeline.rosterBench.store')
                                            <button type="button" class="btn btn-xs btn-info"
                                                @click="editCredentialing(credentialing, 'credentialingPhysician')"
                                            >
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        @endpermission
                                        @permission('admin.accounts.pipeline.rosterBench.removeCredentialing')
                                            <button type="button" class="btn btn-xs btn-danger"
                                                data-toggle="modal" data-target="#credentialingModal"
                                                @click="setCredentialing(credentialing)"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endpermission
                                        @permission('admin.accounts.pipeline.rosterBench.complete')
                                            <button type="button" class="btn btn-xs btn-success"
                                                @click="completeCredentialing(credentialing)"
                                            >
                                                <i class="fa fa-check"></i>
                                            </button>
                                        @endpermission
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="hidden-print" v-show="credentialingPhysician.id">
                                <tr>
                                    <td>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingPhysician.name" required readonly />
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" v-model="credentialingPhysician.hours" min="0" required readonly />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-uppercase" v-model="credentialingPhysician.contract" required readonly />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control datepicker" v-model="credentialingPhysician.fileToCredentialing" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control datepicker" v-model="credentialingPhysician.appToHospital" />
                                    </td>
                                    <td>
                                        <select class="form-control" v-model="credentialingPhysician.stage">
                                            <option :value="null" disabled selected></option>
                                            @for($x = 1; $x <= 12; $x++);
                                                <option value="{{$x}}">{{$x}}</option>
                                            @endfor
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control datepicker" v-model="credentialingPhysician.privilegeGoal" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingPhysician.enrollmentStatus" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingPhysician.enrollmentNotes" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingPhysician.credentialingNotes" />
                                    </td>
                                    <td class="text-center">
                                        @permission('admin.accounts.pipeline.rosterBench.store')
                                            <button type="submit" class="btn btn-xs btn-success">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        @endpermission
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </form>
            </div>

            <div class="no-break-inside">
                <h6 class="pseudo-header bg-gray">@lang('APPs')</h6>
                <form @submit.prevent="addCredentialing('credentialingApp')">
                    <div class="table-responsive">
                        <table class="table table-bordered summary-datatable">
                            <thead class="bg-gray">
                                <tr>
                                    <th class="mw60"></th>
                                    <th class="mw180">@lang('Name')</th>
                                    <th class="mw50">@lang('Hours')</th>
                                    <th class="mw80">@lang('FT/PT/EMB')</th>
                                    <th class="mw120">@lang('File To Credentialing')</th>
                                    <th class="mw100">@lang('APP To Hospital')</th>
                                    <th class="mw50">@lang('Stage')</th>
                                    <th class="mw100">@lang('Privilege Goal')</th>
                                    <th class="mw120">@lang('Enrollment Status')</th>
                                    <th class="mw150">@lang('Enrollment Notes')</th>
                                    <th class="mw150">@lang('Credentialing Notes')</th>
                                    <th class="mw100 text-center hidden-print">@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(credentialing, index) in credentialingApps">
                                    <td>
                                        <input type='radio' class="icheck-red" :name="'stopLight' + index" value="red"/ v-model="credentialing.stopLight" @change="updateStopLight(credentialing, 'red')">
                                        <input type='radio' class="icheck-yellow" :name="'stopLight' + index" value="yellow"/ v-model="credentialing.stopLight" @change="updateStopLight(credentialing, 'yellow')">
                                        <input type='radio' class="icheck-green" :name="'stopLight' + index" value="green"/ v-model="credentialing.stopLight" @change="updateStopLight(credentialing, 'green')">
                                    </td>
                                    <td>@{{ credentialing.name }}</td>
                                    <td>@{{ credentialing.hours }}</td>
                                    <td class="text-uppercase">@{{ credentialing.contract }}</td>
                                    <td>@{{ moment(credentialing.fileToCredentialing) }}</td>
                                    <td>@{{ moment(credentialing.appToHospital) }}</td>
                                    <td>@{{ credentialing.stage }}</td>
                                    <td>@{{ moment(credentialing.privilegeGoal) }}</td>
                                    <td>@{{ credentialing.enrollmentStatus }}</td>
                                    <td>@{{ credentialing.enrollmentNotes }}</td>
                                    <td>@{{ credentialing.credentialingNotes }}</td>
                                    <td class="text-center hidden-print">
                                        @permission('admin.accounts.pipeline.rosterBench.store')
                                            <button type="button" class="btn btn-xs btn-info"
                                                @click="editCredentialing(credentialing, 'credentialingApp')"
                                            >
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        @endpermission
                                        @permission('admin.accounts.pipeline.rosterBench.removeCredentialing')
                                            <button type="button" class="btn btn-xs btn-danger"
                                                data-toggle="modal" data-target="#credentialingModal"
                                                @click="setCredentialing(credentialing)"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endpermission
                                        @permission('admin.accounts.pipeline.rosterBench.complete')
                                            <button type="button" class="btn btn-xs btn-success"
                                                @click="completeCredentialing(credentialing)"
                                            >
                                                <i class="fa fa-check"></i>
                                            </button>
                                        @endpermission
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="hidden-print" v-show="credentialingApp.id">
                                <tr>
                                    <td>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingApp.name" required readonly />
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" v-model="credentialingApp.hours" min="0" required readonly />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-uppercase" v-model="credentialingApp.contract" required readonly />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control datepicker" v-model="credentialingApp.fileToCredentialing" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control datepicker" v-model="credentialingApp.appToHospital" />
                                    </td>
                                    <td>
                                        <select class="form-control" v-model="credentialingApp.stage">
                                            <option :value="null" disabled selected></option>
                                            @for($x = 1; $x <= 12; $x++);
                                                <option value="{{$x}}">{{$x}}</option>
                                            @endfor
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control datepicker" v-model="credentialingApp.privilegeGoal" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingApp.enrollmentStatus" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingApp.enrollmentNotes" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingApp.credentialingNotes" />
                                    </td>
                                    <td class="text-center">
                                        @permission('admin.accounts.pipeline.rosterBench.store')
                                            <button type="submit" class="btn btn-xs btn-success">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        @endpermission
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </form>
            </div>
        @endif


        <div class="no-break-inside">
            <h4 class="pipeline-blue-title">@lang('Current Roster')</h4>
            <h6 class="pseudo-header bg-gray">@lang('Physician')</h6>
            <form @submit.prevent="addRosterBench('roster', 'physician', 'rosterPhysician')">
                <div class="table-responsive">
                    <table id="rosterPhysicianTable" class="table table-bordered">
                        <thead class="bg-gray">
                            <tr>
                                <th class="mw50">@lang('SMD')</th>
                                <th class="mw50">@lang('AMD')</th>
                                <th class="mw200">@lang('Name')</th>
                                <th class="mw50">@lang('Hours')</th>
                                <th class="mw60">@lang('FT/PTG/EMB')</th>
                                <th class="mw50">@lang('NOC')</th>
                                <th class="mw100">@lang('Interview')</th>
                                <th class="mw100">@lang('Contract Out')</th>
                                <th class="mw100">@lang('Contract In')</th>
                                <th class="mw120">@lang('Projected Start Date')</th>
                                <th class="mw200 w100">@lang('Last Contact Date & Next Steps')</th>
                                <th class="mw60">@lang('Signed Not Started')</th>
                                <th class="mw60">@lang('Proactive')</th>
                                <th class="mw150">@lang('Provisional Privilege Start')</th>
                                <th class="mw250 text-center hidden-print">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="roster in activeRosterPhysicians" :class="{'highlight': (roster.signedNotStarted && compareFirstShift(roster.firstShift)), 'bold': roster.providerId}">
                                <td>
                                    <input class="roster-radio" type="checkbox" name="SMD" :value="1" :checked='roster.isSMD' @change="updateRosterBench(roster, 'SMD')">
                                    <span class="hidden">@{{roster.isSMD}}</span>
                                </td>
                                <td>
                                    <input class="roster-radio" type="checkbox" name="AMD" :value="1" :checked='roster.isAMD' @change="updateRosterBench(roster, 'AMD')">
                                    <span class="hidden">@{{roster.isAMD}}</span>
                                </td>
                                <td>@{{ roster.name }}</td>
                                <td>@{{ roster.hours }}</td>
                                <td class="text-uppercase">@{{ roster.contract }}</td>
                                <td>
                                    <input type="checkbox" v-model="roster.noc" @change="updateHighLight(roster)">
                                    <span class="hidden">@{{roster.noc}}</span>
                                </td>
                                <td>@{{ moment(roster.interview) }}</td>
                                <td>@{{ moment(roster.contractOut) }}</td>
                                <td>@{{ moment(roster.contractIn) }}</td>
                                <td>@{{ moment(roster.firstShift) }}</td>
                                <td>@{{ roster.notes }}</td>
                                <td>
                                    <input type="checkbox" v-model="roster.signedNotStarted" @change="updateHighLight(roster)" @click="checkFirstShift(roster, $event)" :readonly="roster.firstShift == null">
                                    <span class="hidden">@{{roster.signedNotStarted}}</span>
                                </td>
                                <td>
                                    <input type="checkbox" v-model="roster.isProactive" @change="updateHighLight(roster)">
                                    <span class="hidden">@{{roster.isProactive}}</span>
                                </td>
                                <td>@{{ moment(roster.provisionalPrivilegeStart) }}</td>
                                <td class="text-center hidden-print">
                                    @permission('admin.accounts.pipeline.rosterBench.store')
                                        <button type="button" class="btn btn-xs btn-info"
                                            @click="editRosterBench(roster, 'rosterPhysician')"
                                        >
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.resign')
                                        <button type="button" class="btn btn-xs btn-warning"
                                            data-toggle="modal" data-target="#resignModal"
                                            @click="setResigning(roster)"
                                        >
                                            @lang('Resign')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.update')
                                        <button type="button" class="btn btn-xs btn-primary"
                                            @click="switchRosterBenchTo(roster, 'bench')"
                                        >
                                            @lang('Bench')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.switch')
                                        <button type="button" class="btn btn-xs btn-primary"
                                            @click="switchRosterBenchTo(roster, 'recruiting', 'phys')"
                                        >
                                            @lang('Recruiting')
                                        </button>
                                    @endpermission
                                    
                                    @permission('admin.accounts.pipeline.rosterBench.destroy')
                                        <button @click="deleteRosterBench(roster)" type="button" class="btn btn-xs btn-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endpermission
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="hidden-print">
                            <tr>
                                <td>
                                    <input type="checkbox" v-model="rosterPhysician.isSMD">
                                </td>
                                <td>
                                    <input type="checkbox" v-model="rosterPhysician.isAMD">
                                </td>
                                <td>
                                    <input type="text" data-id="rosterPhysician" class="form-control providers" v-model="rosterPhysician.name">
                                </td>
                                <td>
                                    <input type="number" class="form-control" v-model="rosterPhysician.hours" min="0" required />
                                </td>
                                <td>
                                    <select class="form-control" v-model="rosterPhysician.contract" required>
                                        <option :value="null" disabled selected></option>
                                        @foreach ($contractTypes as $name => $contractType)
                                            @if($name == 'PT')
                                                <option value="ptg">PTG</option>
                                            @else
                                                <option value="{{ $contractType }}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="checkbox" v-model="rosterPhysician.noc">
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="rosterPhysician.interview" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="rosterPhysician.contractOut" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="rosterPhysician.contractIn" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="rosterPhysician.firstShift" />
                                </td>
                                <td>
                                    <input type="text" class="form-control" v-model="rosterPhysician.notes" />
                                </td>
                                <td>
                                    <input type="checkbox" :disabled="!rosterPhysician.firstShift" v-model="rosterPhysician.signedNotStarted">
                                </td>
                                <td>
                                    <input type="checkbox" v-model="rosterPhysician.isProactive">
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="rosterPhysician.provisionalPrivilegeStart" />
                                </td>
                                <td class="text-center">
                                    @permission('admin.accounts.pipeline.rosterBench.store')
                                        <button type="submit" class="btn btn-xs btn-success">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    @endpermission
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </form>
        </div>

        <div class="no-break-inside">
            <h6 class="pseudo-header bg-gray">@lang('APPs')</h6>
            <form @submit.prevent="addRosterBench('roster', 'app', 'rosterApps')">
                <div class="table-responsive">
                    <table id="rosterAppsTable" class="table table-bordered summary-datatable">
                        <thead class="bg-gray">
                            <tr>
                                <th class="mw100">@lang('Chief')</th>
                                <th class="mw200">@lang('Name')</th>
                                <th class="mw50">@lang('Hours')</th>
                                <th class="mw60">@lang('FT/PTG/EMB')</th>
                                <th class="mw50">@lang('NOC')</th>
                                <th class="mw100">@lang('Interview')</th>
                                <th class="mw100">@lang('Contract Out')</th>
                                <th class="mw100">@lang('Contract In')</th>
                                <th class="mw120">@lang('Projected Start Date')</th>
                                <th class="mw200 w100">@lang('Last Contact Date & Next Steps')</th>
                                <th class="mw60">@lang('Signed Not Started')</th>
                                <th class="mw60">@lang('Proactive')</th>
                                <th class="mw150">@lang('Provisional Privilege Start')</th>
                                <th class="mw250 text-center hidden-print">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="roster in activeRosterApps" :class="{'highlight': roster.signedNotStarted && compareFirstShift(roster.firstShift), 'bold': roster.providerId}">
                                <td>
                                    <input type="checkbox" v-model="roster.isChief" @click="updateRosterBench(roster, 'Chief')">
                                    <span class="hidden">@{{roster.isChief}}</span>
                                </td>
                                <td>@{{ roster.name }}</td>
                                <td>@{{ roster.hours }}</td>
                                <td class="text-uppercase">@{{ roster.contract }}</td>
                                <td>
                                    <input type="checkbox" v-model="roster.noc" @change="updateHighLight(roster)">
                                    <span class="hidden">@{{roster.noc}}</span>
                                </td>
                                <td>@{{ moment(roster.interview) }}</td>
                                <td>@{{ moment(roster.contractOut) }}</td>
                                <td>@{{ moment(roster.contractIn) }}</td>
                                <td>@{{ moment(roster.firstShift) }}</td>
                                <td>@{{ roster.notes }}</td>
                                <td>
                                    <input type="checkbox" v-model="roster.signedNotStarted" @change="updateHighLight(roster)" @click="checkFirstShift(roster, $event)" :readonly="roster.firstShift == null">
                                    <span class="hidden">@{{roster.signedNotStarted}}</span>
                                </td>
                                <td>
                                    <input type="checkbox" v-model="roster.isProactive" @change="updateHighLight(roster)">
                                    <span class="hidden">@{{roster.isProactive}}</span>
                                </td>
                                <td>@{{ moment(roster.provisionalPrivilegeStart) }}</td>
                                <td class="text-center hidden-print">
                                    @permission('admin.accounts.pipeline.rosterBench.store')
                                        <button type="button" class="btn btn-xs btn-info"
                                            @click="editRosterBench(roster, 'rosterApps')"
                                        >
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.resign')
                                        <button type="button" class="btn btn-xs btn-warning"
                                            data-toggle="modal" data-target="#resignModal"
                                            @click="setResigning(roster)"
                                        >
                                            @lang('Resign')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.update')
                                        <button type="button" class="btn btn-xs btn-primary"
                                            @click="switchRosterBenchTo(roster, 'bench')"
                                        >
                                            @lang('Bench')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.switch')
                                        <button type="button" class="btn btn-xs btn-primary"
                                            @click="switchRosterBenchTo(roster, 'recruiting', 'app')"
                                        >
                                            @lang('Recruiting')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.destroy')
                                        <button @click="deleteRosterBench(roster)" type="button" class="btn btn-xs btn-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endpermission
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="hidden-print">
                            <tr>
                                <td>
                                    <input type="checkbox" v-model="rosterApps.isChief" />
                                </td>
                                <td>
                                    <input type="text" data-id="rosterApps" class="form-control providers" v-model="rosterApps.name">
                                </td>
                                <td>
                                    <input type="number" class="form-control" v-model="rosterApps.hours" min="0" required />
                                </td>
                                <td>
                                    <select class="form-control" v-model="rosterApps.contract" required>
                                        <option :value="null" disabled selected></option>
                                        @foreach ($contractTypes as $name => $contractType)
                                            @if($name == 'PT')
                                                <option value="ptg">PTG</option>
                                            @else
                                                <option value="{{ $contractType }}">{{ $name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="checkbox" v-model="rosterApps.noc">
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="rosterApps.interview" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="rosterApps.contractOut" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="rosterApps.contractIn" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="rosterApps.firstShift" />
                                </td>
                                <td>
                                    <input type="text" class="form-control" v-model="rosterApps.notes" />
                                </td>
                                <td>
                                    <input type="checkbox" :disabled="!rosterApps.firstShift" v-model="rosterApps.signedNotStarted">
                                </td>
                                <td>
                                    <input type="checkbox" v-model="rosterApps.isProactive">
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="rosterApps.provisionalPrivilegeStart" />
                                </td>
                                <td class="text-center">
                                    @permission('admin.accounts.pipeline.rosterBench.store')
                                        <button type="submit" class="btn btn-xs btn-success">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    @endpermission
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </form>
        </div>


        <hr />


        <div class="no-break-inside">
            <h4 class="pipeline-blue-title">@lang('Current Bench')</h4>
            <h6 class="pseudo-header bg-gray">@lang('Physician')</h6>
            <form @submit.prevent="addRosterBench('bench', 'physician', 'benchPhysician')">
                <div class="table-responsive">
                    <table id="benchPhysicianTable" class="table table-bordered summary-datatable">
                        <thead class="bg-gray">
                            <tr>
                                <th class="mw200">@lang('Name')</th>
                                <th class="mw70">@lang('Hours')</th>
                                <th class="mw100">@lang('PRN/Locum')</th>
                                <th class="mw50">@lang('NOC')</th>
                                <th class="mw100">@lang('Interview')</th>
                                <th class="mw100">@lang('Contract Out')</th>
                                <th class="mw100">@lang('Contract In')</th>
                                <th class="mw120">@lang('Projected Start Date')</th>
                                <th class="mw200 w100">@lang('Last Contact Date & Next Steps')</th>
                                <th class="mw100">@lang('Signed Not Started')</th>
                                <th class="mw150">@lang('Provisional Privilege Start')</th>
                                <th class="mw250 text-center hidden-print">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="bench in activeBenchPhysicians" :class="{'highlight': bench.signedNotStarted && compareFirstShift(bench.firstShift), 'bold': bench.providerId}">
                                <td>@{{ bench.name }}</td>
                                <td>@{{ bench.hours }}</td>
                                <td class="text-uppercase">@{{ bench.contract }}</td>
                                <td>
                                    <input type="checkbox" v-model="bench.noc" @change="updateHighLight(bench)">
                                    <span class="hidden">@{{bench.noc}}</span>
                                </td>
                                <td>@{{ moment(bench.interview) }}</td>
                                <td>@{{ moment(bench.contractOut) }}</td>
                                <td>@{{ moment(bench.contractIn) }}</td>
                                <td>@{{ moment(bench.firstShift) }}</td>
                                <td>@{{ bench.notes }}</td>
                                <td>
                                    <input type="checkbox" v-model="bench.signedNotStarted" @change="updateHighLight(bench)" @click="checkFirstShift(bench, $event)" :readonly="bench.firstShift == null">
                                    <span class="hidden">@{{bench.signedNotStarted}}</span>
                                </td>
                                <td>@{{ moment(bench.provisionalPrivilegeStart) }}</td>
                                <td class="text-center hidden-print">
                                    @permission('admin.accounts.pipeline.rosterBench.store')
                                        <button type="button" class="btn btn-xs btn-info"
                                            @click="editRosterBench(bench, 'benchPhysician')"
                                        >
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.resign')
                                        <button type="button" class="btn btn-xs btn-warning"
                                            data-toggle="modal" data-target="#resignModal"
                                            @click="setResigning(bench)"
                                        >
                                            @lang('Resign')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.update')
                                        <button type="button" class="btn btn-xs btn-primary"
                                            @click="switchRosterBenchTo(bench, 'roster')"
                                        >
                                            @lang('Roster')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.switch')
                                        <button type="button" class="btn btn-xs btn-primary"
                                            @click="switchRosterBenchTo(bench, 'recruiting', 'phys')"
                                        >
                                            @lang('Recruiting')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.destroy')
                                        <button @click="deleteRosterBench(bench)" type="button" class="btn btn-xs btn-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endpermission
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="hidden-print">
                            <tr>
                                <td>
                                    <input type="text" data-id="benchPhysician" class="form-control providers" v-model="benchPhysician.name">
                                </td>
                                <td>
                                    <input type="number" class="form-control" v-model="benchPhysician.hours" min="0" required />
                                </td>
                                <td>
                                    <select class="form-control" v-model="benchPhysician.contract" required>
                                        <option :value="null" disabled selected></option>
                                        @foreach ($benchContractTypes as $name => $benchContractType)
                                            <option value="{{ $benchContractType }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="checkbox" v-model="benchPhysician.noc">
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="benchPhysician.interview" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="benchPhysician.contractOut" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="benchPhysician.contractIn" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="benchPhysician.firstShift" />
                                </td>
                                <td>
                                    <input type="text" class="form-control" v-model="benchPhysician.notes" />
                                </td>
                                <td>
                                    <input type="checkbox" :disabled="!benchPhysician.firstShift" v-model="benchPhysician.signedNotStarted">
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="benchPhysician.provisionalPrivilegeStart" />
                                </td>
                                <td class="text-center">
                                    @permission('admin.accounts.pipeline.rosterBench.store')
                                        <button type="submit" class="btn btn-xs btn-success">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    @endpermission
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </form>
        </div>

        <div class="no-break-inside">
            <h6 class="pseudo-header bg-gray">@lang('APPs')</h6>
            <form @submit.prevent="addRosterBench('bench', 'app', 'benchApps')">
                <div class="table-responsive">
                    <table id="benchAppsTable" class="table table-bordered summary-datatable">
                        <thead class="bg-gray">
                            <tr>
                                <th class="mw200">@lang('Name')</th>
                                <th class="mw70">@lang('Hours')</th>
                                <th class="mw100">@lang('PRN/Locum')</th>
                                <th class="mw50">@lang('NOC')</th>
                                <th class="mw100">@lang('Interview')</th>
                                <th class="mw100">@lang('Contract Out')</th>
                                <th class="mw100">@lang('Contract In')</th>
                                <th class="mw120">@lang('Projected Start Date')</th>
                                <th class="mw200 w100">@lang('Last Contact Date & Next Steps')</th>
                                <th class="mw100">@lang('Signed Not Started')</th>
                                <th class="mw150">@lang('Provisional Privilege Start')</th>
                                <th class="mw250 text-center hidden-print">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="bench in activeBenchApps" :class="{'highlight': bench.signedNotStarted && compareFirstShift(bench.firstShift), 'bold': bench.providerId}">
                                <td>@{{ bench.name }}</td>
                                <td>@{{ bench.hours }}</td>
                                <td class="text-uppercase">@{{ bench.contract }}</td>
                                <td>
                                    <input type="checkbox" v-model="bench.noc" @change="updateHighLight(bench)">
                                    <span class="hidden">@{{bench.noc}}</span>
                                </td>
                                <td>@{{ moment(bench.interview) }}</td>
                                <td>@{{ moment(bench.contractOut) }}</td>
                                <td>@{{ moment(bench.contractIn) }}</td>
                                <td>@{{ moment(bench.firstShift) }}</td>
                                <td>@{{ bench.notes }}</td>
                                <td>
                                    <input type="checkbox" v-model="bench.signedNotStarted" @change="updateHighLight(bench)" @click="checkFirstShift(bench, $event)" :readonly="bench.firstShift == null">
                                    <span class="hidden">@{{bench.signedNotStarted}}</span>
                                </td>
                                <td>@{{ moment(bench.provisionalPrivilegeStart) }}</td>
                                <td class="text-center hidden-print">
                                    @permission('admin.accounts.pipeline.rosterBench.store')
                                        <button type="button" class="btn btn-xs btn-info"
                                            @click="editRosterBench(bench, 'benchApps')"
                                        >
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.resign')
                                        <button type="button" class="btn btn-xs btn-warning"
                                            data-toggle="modal" data-target="#resignModal"
                                            @click="setResigning(bench)"
                                        >
                                            @lang('Resign')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.update')
                                        <button type="button" class="btn btn-xs btn-primary"
                                            @click="switchRosterBenchTo(bench, 'roster')"
                                        >
                                            @lang('Roster')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.switch')
                                        <button type="button" class="btn btn-xs btn-primary"
                                            @click="switchRosterBenchTo(bench, 'recruiting', 'app')"
                                        >
                                            @lang('Recruiting')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.destroy')
                                        <button @click="deleteRosterBench(bench)" type="button" class="btn btn-xs btn-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endpermission
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="hidden-print">
                            <tr>
                                <td>
                                    <input type="text" data-id="benchApps" class="form-control providers" v-model="benchApps.name">
                                </td>
                                <td>
                                    <input type="number" class="form-control" v-model="benchApps.hours" min="0" required />
                                </td>
                                <td>
                                    <select class="form-control" v-model="benchApps.contract" required>
                                        <option :value="null" disabled selected></option>
                                        @foreach ($benchContractTypes as $name => $benchContractType)
                                            <option value="{{ $benchContractType }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="checkbox" v-model="benchApps.noc">
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="benchApps.interview" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="benchApps.contractOut" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="benchApps.contractIn" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="benchApps.firstShift" />
                                </td>
                                <td>
                                    <input type="text" class="form-control" v-model="benchApps.notes" />
                                </td>
                                <td>
                                    <input type="checkbox" :disabled="!benchApps.firstShift" v-model="benchApps.signedNotStarted">
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="benchApps.provisionalPrivilegeStart" />
                                </td>
                                <td class="text-center">
                                    @permission('admin.accounts.pipeline.rosterBench.store')
                                        <button type="submit" class="btn btn-xs btn-success">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    @endpermission
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </form>
        </div>


        <hr />


        <div class="no-break-inside">
            <h4 class="pipeline-green-title">@lang('Recruiting Pipeline')</h4>
            <form @submit.prevent="addRecruiting">
                <div class="table-responsive">
                    <table id="recruitingsTable" class="table table-bordered summary-datatable">
                        <thead class="bg-gray">
                            <tr>
                                <th class="mw60">@lang('PHYS/APP')</th>
                                <th class="mw200">@lang('Name')</th>
                                <th class="mw60">@lang('FT/PT/EMB')</th>
                                <th class="mw50">@lang('NOC')</th>
                                <th class="mw100">@lang('Interview')</th>
                                <th class="mw100">@lang('Contract Out')</th>
                                <th class="mw100">@lang('Contract In')</th>
                                <th class="mw120">@lang('Projected Start Date')</th>
                                <th class="mw200 w100">@lang('Last Contact Date & Next Steps')</th>
                                <th class="mw250 text-center hidden-print">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="recruiting in sortedRecruitings" :class="{'bg-success': currentRecruiting(recruiting), 'bold': recruiting.providerId}">
                                <td class="text-uppercase">@{{ recruiting.type }}</td>
                                <td>@{{ recruiting.name }}</td>
                                <td class="text-uppercase">@{{ recruiting.contract }}</td>
                                <td>
                                    <input type="checkbox" v-model="recruiting.noc" @change="updateRecruitingNoc(recruiting)">
                                    <span class="hidden">@{{recruiting.noc}}</span>
                                </td>
                                <td>@{{ moment(recruiting.interview) }}</td>
                                <td>@{{ moment(recruiting.contractOut) }}</td>
                                <td>@{{ moment(recruiting.contractIn) }}</td>
                                <td>@{{ moment(recruiting.firstShift) }}</td>
                                <td>@{{ recruiting.notes }}</td>
                                <td class="text-center hidden-print">
                                    @permission('admin.accounts.pipeline.recruiting.store')
                                        <button type="button" class="btn btn-xs btn-info mb5"
                                            @click="editRecruiting(recruiting)"
                                        >
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.recruiting.switch')
                                        <button @click="switchRecruitingTo(recruiting, 'roster')" type="button" class="btn btn-xs btn-primary mb5">
                                            @lang('Roster')
                                        </button>
                                        
                                        <button @click="switchRecruitingTo(recruiting, 'bench')" type="button" class="btn btn-xs btn-primary mb5">
                                            @lang('Bench')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.recruiting.decline')
                                        <button type="button" class="btn btn-xs btn-warning mb5"
                                            data-toggle="modal" data-target="#declineModal"
                                            @click="setDeclining(recruiting)"
                                        >
                                            @lang('Decline')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.recruiting.destroy')
                                        <button @click="deleteRecruiting(recruiting)" type="button" class="btn btn-xs btn-danger mb5">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endpermission
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="hidden-print">
                            <tr>
                                <td>
                                    <select class="form-control" v-model="newRecruiting.type" required>
                                        <option :value="null" disabled selected></option>
                                        @foreach ($recruitingTypes as $name => $recruitingType)
                                            <option value="{{ $recruitingType }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control providers" v-model="newRecruiting.name">
                                </td>
                                <td>
                                    <select class="form-control" v-model="newRecruiting.contract" required>
                                        <option :value="null" disabled selected></option>
                                        @foreach ($contractTypes as $name => $contractType)
                                            <option value="{{ $contractType }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="checkbox" v-model="newRecruiting.noc">
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="newRecruiting.interview" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="newRecruiting.contractOut" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="newRecruiting.contractIn" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="newRecruiting.firstShift" />
                                </td>
                                <td>
                                    <input type="text" class="form-control" v-model="newRecruiting.notes" />
                                </td>
                                <td class="text-center">
                                    @permission('admin.accounts.pipeline.recruiting.decline')
                                        <button type="submit" class="btn btn-xs btn-success">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    @endpermission
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </form>
        </div>


        <div class="no-break-inside">
            <h4 class="pipeline-green-title">@lang('Locums Pipeline')</h4>
            <form @submit.prevent="addLocum">
                <div class="table-responsive">
                    <table id="locumsTable" class="table table-bordered summary-datatable">
                        <thead class="bg-gray">
                            <tr>
                                <th class="mw60">@lang('PHYS/APP')</th>
                                <th class="mw200">@lang('Name')</th>
                                <th class="mw100">@lang('Agency')</th>
                                <th class="mw100">@lang('Potential Start')</th>
                                <th class="mw200 w50">@lang('Credentialing Notes')</th>
                                <th class="mw70">@lang('Shifts')</th>
                                <th class="mw100">@lang('Start Date')</th>
                                <th class="mw200 w50">@lang('Comments')</th>
                                <th class="mw250 text-center hidden-print">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="locum in sortedLocums" :class="{'bold': locum.providerId}">
                                <td class="text-uppercase">@{{ locum.type }}</td>
                                <td>@{{ locum.name }}</td>
                                <td>@{{ locum.agency }}</td>
                                <td>@{{ moment(locum.potentialStart) }}</td>
                                <td>@{{ locum.credentialingNotes }}</td>
                                <td>@{{ locum.shiftsOffered }}</td>
                                <td>@{{ moment(locum.startDate) }}</td>
                                <td>@{{ locum.comments }}</td>
                                <td class="text-center hidden-print">
                                    @permission('admin.accounts.pipeline.locum.store')
                                        <button type="button" class="btn btn-xs btn-info"
                                            @click="editLocum(locum)"
                                        >
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.locum.switch')
                                        <button @click="switchLocumTo(locum, 'roster')" type="button" class="btn btn-xs btn-primary mb5">
                                            @lang('Roster')
                                        </button>
                                        
                                        <button @click="switchLocumTo(locum, 'bench')" type="button" class="btn btn-xs btn-primary mb5">
                                            @lang('Bench')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.locum.decline')
                                        <button type="button" class="btn btn-xs btn-warning"
                                            data-toggle="modal" data-target="#declineModal"
                                            @click="setDeclining(locum)"
                                        >
                                            @lang('Decline')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.locum.destroy')
                                        <button @click="deleteLocum(locum)" type="button" class="btn btn-xs btn-danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    @endpermission
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="hidden-print">
                            <tr>
                                <td>
                                    <select class="form-control" v-model="newLocum.type" required>
                                        <option :value="null" disabled selected></option>
                                        @foreach ($recruitingTypes as $name => $recruitingType)
                                            <option value="{{ $recruitingType }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control providers" v-model="newLocum.name">
                                </td>
                                <td>
                                    <input type="text" class="form-control" v-model="newLocum.agency" required />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="newLocum.potentialStart" />
                                </td>
                                <td>
                                    <input type="text" class="form-control" v-model="newLocum.credentialingNotes" />
                                </td>
                                <td>
                                    <input type="number" class="form-control" v-model="newLocum.shiftsOffered" min="0" />
                                </td>
                                <td>
                                    <input type="text" class="form-control datepicker" v-model="newLocum.startDate" />
                                </td>
                                <td>
                                    <input type="text" class="form-control" v-model="newLocum.comments" />
                                </td>
                                <td class="text-center">
                                    @permission('admin.accounts.pipeline.locum.store')
                                        <button type="submit" class="btn btn-xs btn-success">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    @endpermission
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </form>
        </div>


        <hr />


        <div class="no-break-inside">
            <h4 class="pipeline-orange-title">@lang('Declined List')</h4>
            <div class="table-responsive">
                <table id="declinedTable" class="table table-bordered">
                    <thead class="bg-gray">
                        <tr>
                            <th class="mw200">@lang('Name')</th>
                            <th class="mw60">@lang('FT/PT/EMB')</th>
                            <th class="mw100">@lang('Interview')</th>
                            <th class="mw100">@lang('Application')</th>
                            <th class="mw100">@lang('Contract Out')</th>
                            <th class="mw100">@lang('Declined')</th>
                            <th class="mw200 w100">@lang('Reason')</th>
                            <th class="mw80 text-center hidden-print">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="declined in declines">
                            <td>@{{ declined.name }}</td>
                            <td class="text-uppercase">@{{ declined.contract }}</td>
                            <td>@{{ moment(declined.interview) }}</td>
                            <td>@{{ moment(declined.application) }}</td>
                            <td>@{{ moment(declined.contractOut) }}</td>
                            <td>@{{ moment(declined.declined) }}</td>
                            <td>@{{ declined.declinedReason }}</td>
                            <td class="text-center hidden-print">
                                @permission('admin.accounts.pipeline.locum.decline')
                                    <button type="button" class="btn btn-xs btn-info"
                                        data-toggle="modal" data-target="#declineModal"
                                        @click="setDeclining(declined)"
                                    >
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                @endpermission
                                @permission('admin.accounts.pipeline.declined.destroy')
                                    <button type="button" class="btn btn-xs btn-danger"
                                        @click="deleteDeclining(declined)"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @endpermission
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal fade" id="declineModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            @lang('Decline')
                            @{{ declining.name }}
                        </h4>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="decline">
                            <div class="form-group">
                                <label for="decliningcontract">@lang('Contract')</label>
                                <select id="decliningcontract" class="form-control" v-model="declining.contract">
                                    <option :value="null" disabled selected></option>
                                    @foreach ($contractTypes as $name => $contractType)
                                        <option value="{{ $contractType }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="declininginterview">@lang('Interview')</label>
                                <input id="declininginterview" type="text" class="form-control datepicker" v-model="declining.interview" />
                            </div>
                            <div class="form-group">
                                <label for="decliningapplication">@lang('Application')</label>
                                <input id="decliningapplication" type="text" class="form-control datepicker" v-model="declining.application" />
                            </div>
                            <div class="form-group">
                                <label for="decliningcontractout">@lang('Contract Out')</label>
                                <input id="decliningcontractout" type="text" class="form-control datepicker" v-model="declining.contractOut" />
                            </div>
                            <div class="form-group">
                                <label for="decliningdeclined">@lang('Declined')</label>
                                <input id="decliningdeclined" type="text" class="form-control datepicker" v-model="declining.declined" required />
                            </div>
                            <div class="form-group">
                                <label for="decliningreason">@lang('Reason')</label>
                                <input id="decliningreason" type="text" class="form-control" v-model="declining.declinedReason" required />
                            </div>
                            <div class="row mt5">
                                <div class="col-xs-6">
                                    <button type="submit" class="btn btn-warning">
                                        @lang('Confirm')
                                    </button>
                                </div>
                                <div class="col-xs-6 text-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="credentialingModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            @lang('Remove')
                            @{{ credentialing.name }}
                        </h4>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="removeCredentialing">
                            <div class="form-group">
                                <label for="removedReason">@lang('Please input reason for removal')</label>
                                <input id="removedReason" type="text" class="form-control" v-model="credentialing.removedReason" required />
                            </div>
                            <div class="row mt5">
                                <div class="col-xs-6">
                                    <button type="submit" class="btn btn-warning">
                                        @lang('Confirm')
                                    </button>
                                </div>
                                <div class="col-xs-6 text-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="no-break-inside">
            <h4 class="pipeline-orange-title">@lang('Resigned List')</h4>
            <div class="table-responsive">
                <table class="table table-bordered summary-datatable">
                    <thead class="bg-gray">
                        <tr>
                            <th class="mw60">@lang('PHYS/APP')</th>
                            <th class="mw200">@lang('Name')</th>
                            <th class="mw100">@lang('Resigned')</th>
                            <th class="mw200 w100">@lang('Reason')</th>
                            <th class="mw80 text-center hidden-print">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="resigned in resigns">
                            <td class="text-uppercase">@{{ resigned.type }}</td>
                            <td>@{{ resigned.name }}</td>
                            <td>@{{ moment(resigned.resigned) }}</td>
                            <td>@{{ resigned.resignedReason }}</td>
                            <td class="text-center hidden-print">
                                @permission('admin.accounts.pipeline.rosterBench.resign')
                                    <button type="button" class="btn btn-xs btn-info"
                                        data-toggle="modal" data-target="#resignModal"
                                        @click="setResigning(resigned)"
                                    >
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                @endpermission
                                @permission('admin.accounts.pipeline.resigned.destroy')
                                    <button type="button" class="btn btn-xs btn-danger"
                                        @click="deleteRosterBench(resigned)"
                                    >
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @endpermission
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal fade" id="resignModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">
                            @lang('Resign')
                            @{{ resigning.name }}
                        </h4>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="resign">
                            <div class="form-group">
                                <label for="resigningtype">@lang('Type')</label>
                                <select id="resigningtype" class="form-control" v-model="resigning.type">
                                    <option :value="null" disabled selected></option>
                                    @foreach ($recruitingTypes as $name => $recruitingType)
                                        <option value="{{ $recruitingType }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="resigningresigned">@lang('Resigned')</label>
                                <input id="resigningresigned" type="text" class="form-control datepicker" v-model="resigning.resigned" required />
                            </div>
                            <div class="form-group">
                                <label for="resigningreason">@lang('Reason')</label>
                                <input id="resigningreason" type="text" class="form-control" v-model="resigning.resignedReason" required />
                            </div>
                            <div class="form-group">
                                <label for="resigninglastshift">@lang('Last Shift')</label>
                                <input id="resigninglastshift" type="text" class="form-control datepicker" v-model="resigning.lastShift" required />
                            </div>
                            <div class="row mt5">
                                <div class="col-xs-6">
                                    <button type="submit" class="btn btn-warning">
                                        @lang('Confirm')
                                    </button>
                                </div>
                                <div class="col-xs-6 text-right">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if (!Auth::user()->hasRoleId(11))
            <hr />

            <div class="no-break-inside">
                <h4 class="pipeline-blue-title">@lang('Credentialing Pipeline')</h4>
                <h6 class="pseudo-header bg-gray">@lang('Physician')</h6>
                <form @submit.prevent="addCredentialing('credentialingPhysician')">
                    <div class="table-responsive">
                        <table class="table table-bordered summary-datatable">
                            <thead class="bg-gray">
                                <tr>
                                    <th class="mw60"></th>
                                    <th class="mw180">@lang('Name')</th>
                                    <th class="mw50">@lang('Hours')</th>
                                    <th class="mw80">@lang('FT/PT/EMB')</th>
                                    <th class="mw120">@lang('File To Credentialing')</th>
                                    <th class="mw100">@lang('APP To Hospital')</th>
                                    <th class="mw50">@lang('Stage')</th>
                                    <th class="mw100">@lang('Privilege Goal')</th>
                                    <th class="mw120">@lang('Enrollment Status')</th>
                                    <th class="mw150">@lang('Enrollment Notes')</th>
                                    <th class="mw150">@lang('Credentialing Notes')</th>
                                    <th class="mw100 text-center hidden-print">@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(credentialing, index) in credentialingPhysicians">
                                    <td>
                                        <input type='radio' class="icheck-red" :name="'stopLight' + index" value="red"/ v-model="credentialing.stopLight" @change="updateStopLight(credentialing, 'red')">
                                        <input type='radio' class="icheck-yellow" :name="'stopLight' + index" value="yellow"/ v-model="credentialing.stopLight" @change="updateStopLight(credentialing, 'yellow')">
                                        <input type='radio' class="icheck-green" :name="'stopLight' + index" value="green"/ v-model="credentialing.stopLight" @change="updateStopLight(credentialing, 'green')">
                                    </td>
                                    <td>@{{ credentialing.name }}</td>
                                    <td>@{{ credentialing.hours }}</td>
                                    <td class="text-uppercase">@{{ credentialing.contract }}</td>
                                    <td>@{{ moment(credentialing.fileToCredentialing) }}</td>
                                    <td>@{{ moment(credentialing.appToHospital) }}</td>
                                    <td>@{{ credentialing.stage }}</td>
                                    <td>@{{ moment(credentialing.privilegeGoal) }}</td>
                                    <td>@{{ credentialing.enrollmentStatus }}</td>
                                    <td>@{{ credentialing.enrollmentNotes }}</td>
                                    <td>@{{ credentialing.credentialingNotes }}</td>
                                    <td class="text-center hidden-print">
                                        @permission('admin.accounts.pipeline.rosterBench.store')
                                            <button type="button" class="btn btn-xs btn-info"
                                                @click="editCredentialing(credentialing, 'credentialingPhysician')"
                                            >
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        @endpermission
                                        @permission('admin.accounts.pipeline.rosterBench.removeCredentialing')
                                            <button type="button" class="btn btn-xs btn-danger"
                                                data-toggle="modal" data-target="#credentialingModal"
                                                @click="setCredentialing(credentialing)"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endpermission
                                        @permission('admin.accounts.pipeline.rosterBench.complete')
                                            <button type="button" class="btn btn-xs btn-success"
                                                @click="completeCredentialing(credentialing)"
                                            >
                                                <i class="fa fa-check"></i>
                                            </button>
                                        @endpermission
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="hidden-print" v-show="credentialingPhysician.id">
                                <tr>
                                    <td>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingPhysician.name" required readonly />
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" v-model="credentialingPhysician.hours" min="0" required readonly />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-uppercase" v-model="credentialingPhysician.contract" required readonly />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control datepicker" v-model="credentialingPhysician.fileToCredentialing" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control datepicker" v-model="credentialingPhysician.appToHospital" />
                                    </td>
                                    <td>
                                        <select class="form-control" v-model="credentialingPhysician.stage">
                                            <option :value="null" disabled selected></option>
                                            @for($x = 1; $x <= 12; $x++);
                                                <option value="{{$x}}">{{$x}}</option>
                                            @endfor
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control datepicker" v-model="credentialingPhysician.privilegeGoal" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingPhysician.enrollmentStatus" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingPhysician.enrollmentNotes" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingPhysician.credentialingNotes" />
                                    </td>
                                    <td class="text-center">
                                        @permission('admin.accounts.pipeline.rosterBench.store')
                                            <button type="submit" class="btn btn-xs btn-success">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        @endpermission
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </form>
            </div>

            <div class="no-break-inside">
                <h6 class="pseudo-header bg-gray">@lang('APPs')</h6>
                <form @submit.prevent="addCredentialing('credentialingApp')">
                    <div class="table-responsive">
                        <table class="table table-bordered summary-datatable">
                            <thead class="bg-gray">
                                <tr>
                                    <th class="mw60"></th>
                                    <th class="mw180">@lang('Name')</th>
                                    <th class="mw50">@lang('Hours')</th>
                                    <th class="mw80">@lang('FT/PT/EMB')</th>
                                    <th class="mw120">@lang('File To Credentialing')</th>
                                    <th class="mw100">@lang('APP To Hospital')</th>
                                    <th class="mw50">@lang('Stage')</th>
                                    <th class="mw100">@lang('Privilege Goal')</th>
                                    <th class="mw120">@lang('Enrollment Status')</th>
                                    <th class="mw150">@lang('Enrollment Notes')</th>
                                    <th class="mw150">@lang('Credentialing Notes')</th>
                                    <th class="mw100 text-center hidden-print">@lang('Actions')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(credentialing, index) in credentialingApps">
                                    <td>
                                        <input type='radio' class="icheck-red" :name="'stopLight' + index" value="red"/ v-model="credentialing.stopLight" @change="updateStopLight(credentialing, 'red')">
                                        <input type='radio' class="icheck-yellow" :name="'stopLight' + index" value="yellow"/ v-model="credentialing.stopLight" @change="updateStopLight(credentialing, 'yellow')">
                                        <input type='radio' class="icheck-green" :name="'stopLight' + index" value="green"/ v-model="credentialing.stopLight" @change="updateStopLight(credentialing, 'green')">
                                    </td>
                                    <td>@{{ credentialing.name }}</td>
                                    <td>@{{ credentialing.hours }}</td>
                                    <td class="text-uppercase">@{{ credentialing.contract }}</td>
                                    <td>@{{ moment(credentialing.fileToCredentialing) }}</td>
                                    <td>@{{ moment(credentialing.appToHospital) }}</td>
                                    <td>@{{ credentialing.stage }}</td>
                                    <td>@{{ moment(credentialing.privilegeGoal) }}</td>
                                    <td>@{{ credentialing.enrollmentStatus }}</td>
                                    <td>@{{ credentialing.enrollmentNotes }}</td>
                                    <td>@{{ credentialing.credentialingNotes }}</td>
                                    <td class="text-center hidden-print">
                                        @permission('admin.accounts.pipeline.rosterBench.store')
                                            <button type="button" class="btn btn-xs btn-info"
                                                @click="editCredentialing(credentialing, 'credentialingApp')"
                                            >
                                                <i class="fa fa-pencil"></i>
                                            </button>
                                        @endpermission
                                        @permission('admin.accounts.pipeline.rosterBench.removeCredentialing')
                                            <button type="button" class="btn btn-xs btn-danger"
                                                data-toggle="modal" data-target="#credentialingModal"
                                                @click="setCredentialing(credentialing)"
                                            >
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endpermission
                                        @permission('admin.accounts.pipeline.rosterBench.complete')
                                            <button type="button" class="btn btn-xs btn-success"
                                                @click="completeCredentialing(credentialing)"
                                            >
                                                <i class="fa fa-check"></i>
                                            </button>
                                        @endpermission
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="hidden-print" v-show="credentialingApp.id">
                                <tr>
                                    <td>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingApp.name" required readonly />
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" v-model="credentialingApp.hours" min="0" required readonly />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control text-uppercase" v-model="credentialingApp.contract" required readonly />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control datepicker" v-model="credentialingApp.fileToCredentialing" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control datepicker" v-model="credentialingApp.appToHospital" />
                                    </td>
                                    <td>
                                        <select class="form-control" v-model="credentialingApp.stage">
                                            <option :value="null" disabled selected></option>
                                            @for($x = 1; $x <= 12; $x++);
                                                <option value="{{$x}}">{{$x}}</option>
                                            @endfor
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control datepicker" v-model="credentialingApp.privilegeGoal" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingApp.enrollmentStatus" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingApp.enrollmentNotes" />
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="credentialingApp.credentialingNotes" />
                                    </td>
                                    <td class="text-center">
                                        @permission('admin.accounts.pipeline.rosterBench.store')
                                            <button type="submit" class="btn btn-xs btn-success">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        @endpermission
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </form>
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    <script>
        $('#accountId').on('change', function() {
            var accountId = $(this).val();

            window.location.href = '/admin/accounts/'+accountId+'/pipeline';
        });

        $(document).ready(function() {
            var accountsDT = $('#rosterPhysicianTable').DataTable($.extend({}, window.defaultDTOptions, {
                order: [[ 0, 'desc' ], [ 1, 'desc' ]]
            }));

            var declinedDT = $('#declinedTable').DataTable($.extend({}, window.defaultDTOptions, {
                order: [[ 5, 'desc' ]]
            }));

            var providers = BackendVars.providers.map(function(provider) {
                return provider.fullName;
            });

            $(".providers").each(function () {
                $(this).autocomplete({
                    source: providers,
                    close: function( event, ui ) {
                        const cEvent = new CustomEvent('input');
                        this.dispatchEvent(cEvent);
                        $(this).blur();
                        $(this).focus();
                    },
                    select: function( event, ui ) {
                        var selected = ui.item.value;
                        var autocompletePlace = $(event.target).data("id");
                        var provider = _.find(BackendVars.providers, {fullName: selected});

                        $.post('/admin/emworks/find', {
                            providerId: provider.id,
                            _token: "{{ csrf_token() }}"
                        }, function(response) {
                            window.app.provider = response;
                            window.app.autocomplete = autocompletePlace;
                        });
                    }
                });
            });
        });

        window.app = new Vue({
            el: '#app',

            data: {
                account: BackendVars.account,
                pipeline: BackendVars.pipeline,
                rostersBenchs: BackendVars.rostersBenchs,
                recruitings: BackendVars.recruitings,
                locums: BackendVars.locums,
                providers: BackendVars.providers,
                provider: null,
                autocomplete: null,

                staffPhysicianNeeds: BackendVars.pipeline.staffPhysicianNeeds,
                staffAppsNeeds: BackendVars.pipeline.staffAppsNeeds,

                fullTimeHoursPhys: BackendVars.pipeline.fullTimeHoursPhys,
                fullTimeHoursApps: BackendVars.pipeline.fullTimeHoursApps,

                oldSMD: BackendVars.rostersBenchs.filter( function(roster) { return roster.isSMD == 1 } ),
                oldAMD: BackendVars.rostersBenchs.filter( function(roster) { return roster.isAMD == 1 } ),
                oldChief: BackendVars.rostersBenchs.filter( function(roster) { return roster.isChief == 1 } ),

                rosterPhysician: {
                    id: null,
                    name: '',
                    hours: '',
                    interview: '',
                    contractOut: '',
                    contractIn: '',
                    firstShift: '',
                    notes: '',
                    provisionalPrivilegeStart: '',
                },

                rosterApps: {
                    id: null,
                    name: '',
                    hours: '',
                    interview: '',
                    contractOut: '',
                    contractIn: '',
                    firstShift: '',
                    notes: '',
                    provisionalPrivilegeStart: '',
                },

                credentialingPhysician: {
                    id: null,
                    name: '',
                    hours: '',
                    fileToCredentialing: '',
                    privilegeGoal: '',
                    appToHospital: '',
                    stage: '',
                    notes: '',
                },

                credentialingApp: {
                    id: null,
                    name: '',
                    hours: '',
                    fileToCredentialing: '',
                    privilegeGoal: '',
                    appToHospital: '',
                    stage: '',
                    notes: '',
                },

                benchPhysician: {
                    name: '',
                    hours: '',
                    interview: '',
                    contractOut: '',
                    contractIn: '',
                    firstShift: '',
                    notes: '',
                    provisionalPrivilegeStart: '',
                },

                benchApps: {
                    name: '',
                    hours: '',
                    interview: '',
                    contractOut: '',
                    contractIn: '',
                    firstShift: '',
                    notes: '',
                    provisionalPrivilegeStart: '',
                },


                newRecruiting: {
                    type: null,
                    name: '',
                    contract: null,
                    interview: '',
                    contractOut: '',
                    contractIn: '',
                    firstShift: '',
                    notes: '',
                },


                newLocum: {
                    type: null,
                    name: '',
                    agency: '',
                    potentialStart: '',
                    credentialingNotes: '',
                    shiftsOffered: '',
                    startDate: '',
                    comments: '',
                },


                declining: {
                    id: null,
                    contract: null,
                    interview: '',
                    application: '',
                    contractOut: '',
                    declined: '',
                    declinedReason: '',
                    instance: '',
                },
                toDecline: {},

                credentialing: {
                    id: null,
                    name: '',
                    hours: '',
                    fileToCredentialing: '',
                    privilegeGoal: '',
                    appToHospital: '',
                    stage: '',
                    notes: '',
                },
                toRemove: {},


                resigning: {
                    id: null,
                    type: null,
                    resigned: '',
                    resignedReason: '',
                },
                toResign: {},
            },

            watch: {
                provider: function (newProvider) {
                    if (this.autocomplete == 'rosterPhysician') {
                        this.rosterPhysician.interview = this.moment(newProvider.interview);
                        this.rosterPhysician.contractIn = this.moment(newProvider.contractIn);
                        this.rosterPhysician.contractOut = this.moment(newProvider.contractOut);
                        this.rosterPhysician.privilegeGoal = this.moment(newProvider.privilegeGoal);
                        this.rosterPhysician.provisionalPrivilegeStart = this.moment(newProvider.ProvisionalPrivilegeStart);
                        this.rosterPhysician.enrollmentStatus = newProvider.EnrollmentStatus;
                        this.rosterPhysician.credentialingNotes = newProvider.credentialingNotes;
                        this.rosterPhysician.fileToCredentialing = this.moment(newProvider.fileToCredentialing);
                        this.rosterPhysician.appToHospital = this.moment(newProvider.appToHospital);
                    } else if (this.autocomplete == 'benchPhysician') {
                        this.benchPhysician.interview = this.moment(newProvider.interview);
                        this.benchPhysician.contractIn = this.moment(newProvider.contractIn);
                        this.benchPhysician.contractOut = this.moment(newProvider.contractOut);
                        this.benchPhysician.privilegeGoal = this.moment(newProvider.privilegeGoal);
                        this.benchPhysician.provisionalPrivilegeStart = this.moment(newProvider.ProvisionalPrivilegeStart);
                        this.benchPhysician.enrollmentStatus = newProvider.EnrollmentStatus;
                        this.benchPhysician.credentialingNotes = newProvider.credentialingNotes;
                        this.benchPhysician.fileToCredentialing = this.moment(newProvider.fileToCredentialing);
                        this.benchPhysician.appToHospital = this.moment(newProvider.appToHospital);
                    } else if (this.autocomplete == 'rosterApps') {
                        this.rosterApps.interview = this.moment(newProvider.interview);
                        this.rosterApps.contractIn = this.moment(newProvider.contractIn);
                        this.rosterApps.contractOut = this.moment(newProvider.contractOut);
                        this.rosterApps.privilegeGoal = this.moment(newProvider.privilegeGoal);
                        this.rosterApps.provisionalPrivilegeStart = this.moment(newProvider.ProvisionalPrivilegeStart);
                        this.rosterApps.enrollmentStatus = newProvider.EnrollmentStatus;
                        this.rosterApps.credentialingNotes = newProvider.credentialingNotes;
                        this.rosterApps.fileToCredentialing = this.moment(newProvider.fileToCredentialing);
                        this.rosterApps.appToHospital = this.moment(newProvider.appToHospital);
                    } else if (this.autocomplete == 'benchApps') {
                        this.benchApps.interview = this.moment(newProvider.interview);
                        this.benchApps.contractIn = this.moment(newProvider.contractIn);
                        this.benchApps.contractOut = this.moment(newProvider.contractOut);
                        this.benchApps.privilegeGoal = this.moment(newProvider.privilegeGoal);
                        this.benchApps.provisionalPrivilegeStart = this.moment(newProvider.ProvisionalPrivilegeStart);
                        this.benchApps.enrollmentStatus = newProvider.EnrollmentStatus;
                        this.benchApps.credentialingNotes = newProvider.credentialingNotes;
                        this.benchApps.fileToCredentialing = this.moment(newProvider.fileToCredentialing);
                        this.benchApps.appToHospital = this.moment(newProvider.appToHospital);
                    }
                }
            },

            computed: {
                SMDOpenings: function () {
                    var SMD = _.chain(this.rostersBenchs)
                        .filter({ isSMD: 1 })
                        .value();

                    return this.account.hasSMD ? (SMD.length ? 0 : 1) : 0;
                },

                staffPhysicianHaves: function () {
                    var result = 0;

                    if(this.pipeline.practiceTime == 'hours') {
                        return _(this.activeRosterPhysicians).sumBy(function(roster){
                            if(roster.contract != 'emb')
                            return roster.hours;
                        });
                    } else {
                        $.each(this.activeRosterPhysicians, function(index, roster) {
                            if (roster.contract == 'ft') {
                                result += 1;
                            } else if(roster.contract != 'ft' && roster.contract != 'emb') {
                                result += 0.5;
                            }
                        });

                        return result;
                    }
                },

                staffAppsHaves: function () {
                    var result = 0;

                    if(this.pipeline.practiceTime == 'hours') {
                        return _(this.activeRosterApps).sumBy(function(roster){
                            if(roster.contract != 'emb')
                            return roster.hours;
                        });
                    } else {
                        $.each(this.activeRosterApps, function(index, roster) {
                            if (roster.contract == 'ft') {
                                result += 1;
                            } else if(roster.contract != 'ft' && roster.contract != 'emb') {
                                result += 0.5;
                            }
                        });
                        
                        return result;
                    }
                },

                staffPhysicianOpenings: function () {
                    var result;

                    if(this.pipeline.practiceTime == 'fte') {
                        result = this.staffPhysicianNeeds - this.staffPhysicianHaves - this.SMDOpenings;

                        return this.roundStep(result, 0.5);
                    }

                    result = this.staffPhysicianNeeds - this.staffPhysicianHaves;

                    return this.roundStep(result, 0.5);
                },

                staffAppsOpenings: function () {
                    var result = this.staffAppsNeeds - this.staffAppsHaves;

                    return this.roundStep(result, 0.5);
                },

                staffPhysicianFTEHaves: function () {
                    if (this.pipeline.practiceTime == 'fte') {
                        var result = 0;

                        $.each(this.activeRosterPhysicians, function(index, roster) {
                            if (roster.contract == 'ft' || roster.contract == 'emb') {
                                result += 1;
                            } else {
                                result += 0.5;
                            }
                        });
                        
                        return result;
                    };

                    if (this.fullTimeHoursPhys == 0) return 0;
                    var result = this.staffPhysicianHaves / this.fullTimeHoursPhys;
                    
                    return result.toFixed(1);
                },

                staffAppsFTEHaves: function () {
                    if (this.pipeline.practiceTime == 'fte') {
                        var result = 0;

                        $.each(this.activeRosterApps, function(index, roster) {
                            if (roster.contract == 'ft' || roster.contract == 'emb') {
                                result += 1;
                            } else {
                                result += 0.5;
                            }
                        });
                        
                        return result;
                    };

                    if (this.fullTimeHoursApps == 0) return 0;
                    var result = this.staffAppsHaves / this.fullTimeHoursApps;
                    
                    return result.toFixed(1);
                },

                staffPhysicianFTENeeds: function () {
                    if (this.fullTimeHoursPhys == 0) return 0;
                    var result = this.staffPhysicianNeeds / this.fullTimeHoursPhys;
                    
                    return result.toFixed(1);
                },

                staffAppsFTENeeds: function () {
                    if (this.fullTimeHoursApps == 0) return 0;
                    var result = this.staffAppsNeeds / this.fullTimeHoursApps;
                    
                    return result.toFixed(1);
                },

                staffPhysicianFTEOpenings: function () {
                    if (this.pipeline.practiceTime == 'fte') {
                        return this.staffPhysicianFTENeeds - this.staffPhysicianFTEHaves - this.SMDOpenings;
                    };

                    if (this.fullTimeHoursPhys == 0) return 0;
                    var result = this.staffPhysicianFTENeeds - this.staffPhysicianFTEHaves  - this.SMDOpenings;
                    
                    return this.roundStep(result, 0.5);
                },

                staffAppsFTEOpenings: function () {
                    if (this.pipeline.practiceTime == 'fte') {
                        return this.staffAppsFTENeeds - this.staffAppsFTEHaves;
                    };
                    if (this.fullTimeHoursApps == 0) return 0;
                    var result = this.staffAppsFTENeeds - this.staffAppsFTEHaves;
                    
                    return this.roundStep(result, 0.5);
                },

                staffPhysicianRecruitedActual: function() {
                    var result = (this.staffPhysicianFTEHaves / this.staffPhysicianFTENeeds) * 100;
                    
                    if (this.staffPhysicianFTENeeds < 1) {
                        result = 0;
                    }

                    return result.toFixed(1) + '%';
                },

                staffPhysicianRecruitedReported: function() {
                    var result = (this.staffPhysicianFTEHaves / this.staffPhysicianFTENeeds) * 100;

                    if (this.staffPhysicianFTENeeds < 1) {
                        result = 0;
                    }

                    if (result > 100) {
                        result = 100;
                    }
                    
                    return result.toFixed(1) + '%';
                },

                staffAppsRecruitedActual: function() {
                    var result = (this.staffAppsFTEHaves / this.staffAppsFTENeeds) * 100;

                    if (this.staffAppsFTENeeds < 1) {
                        result = 0;
                    }

                    return result.toFixed(1) + '%';
                },

                staffAppsRecruitedReported: function() {
                    var result = (this.staffAppsFTEHaves / this.staffAppsFTENeeds) * 100;

                    if (this.staffAppsFTENeeds < 1) {
                        result = 0;
                    }
                    
                    if (result > 100) {
                        result = 100;
                        return result.toFixed(1) + '%';
                    }

                    return result.toFixed(1) + '%';
                },

                activeRosterPhysicians: function () {
                    return _.chain(this.rostersBenchs)
                        .filter({ place: 'roster', activity: 'physician' })
                        .reject('resigned')
                        .orderBy(['isSMD', 'isAMD', 'name'], ['desc', 'desc', 'asc'])
                        .value();
                },

                activeRosterApps: function () {
                    return _.chain(this.rostersBenchs)
                        .filter({ place: 'roster', activity: 'app' })
                        .reject('resigned')
                        .orderBy(['isChief', 'name'], ['desc', 'asc'])
                        .value();
                },

                activeBenchPhysicians: function () {
                    return _.chain(this.rostersBenchs)
                        .filter({ place: 'bench', activity: 'physician' })
                        .reject('resigned')
                        .value();
                },

                activeBenchApps: function () {
                    return _.chain(this.rostersBenchs)
                        .filter({ place: 'bench', activity: 'app' })
                        .reject('resigned')
                        .value();
                },

                credentialingPhysicians: function () {
                    return _.chain(this.rostersBenchs)
                        .filter(function(credentialing) {
                            return credentialing.activity === 'physician' && (
                                credentialing.signedNotStarted === 1 || credentialing.fileToCredentialing !== null
                            );
                        })
                        .reject('resigned')
                        .reject('completed')
                        .reject('removed')
                        .value();
                },

                credentialingApps: function () {
                    return _.chain(this.rostersBenchs)
                        .filter(function(credentialing) {
                            return credentialing.activity === 'app' && (
                                credentialing.signedNotStarted === 1 || credentialing.fileToCredentialing !== null
                            );
                        })
                        .reject('resigned')
                        .reject('completed')
                        .reject('removed')
                        .value();
                },

                sortedRecruitings: function () {
                    return _.chain(this.recruitings).reject('declined')
                        .orderBy(['type', function (recruiting) {
                            return recruiting.name.toLowerCase();
                        }], ['desc', 'asc']).value();
                },

                sortedLocums: function () {
                    return _.chain(this.locums).reject('declined')
                        .orderBy(['type', function (locum) {
                            return locum.name.toLowerCase();
                        }], ['desc', 'asc']).value();
                },

                declines: function () {
                    return _.chain(this.recruitings)
                        .concat(this.locums)
                        .filter('declined').value();
                },

                resigns: function () {
                    return _.filter(this.rostersBenchs, 'resigned');
                },
            },

            methods: {
                setProvider: function(obj) {
                    this.provider = obj;
                },

                addRosterBench: function (place, activity, entity) {
                    if(entity == 'rosterPhysician') {
                        this[entity].oldSMD = this.oldSMD.length ? this.oldSMD[0].id : '';
                        this[entity].oldAMD = this.oldAMD.length ? this.oldAMD[0].id : '';

                        if( this.oldSMD.length && this[entity].isSMD) {
                            this.oldSMD[0].isSMD = 0;
                        }
                        
                        if( this.oldAMD.length && this[entity].isAMD) {
                            this.oldAMD[0].isAMD = 0;
                        }
                    }

                    if(entity == 'rosterApps') {
                        this[entity].oldChief = this.oldChief.length ? this.oldChief[0].id : '';

                        if( this.oldChief.length && this[entity].isChief) {
                            this.oldChief[0].isChief = 0;
                        }
                    }

                    var name = this[entity].name;
                    var provider = _.find(this.providers, {fullName: name});
                    
                    this[entity].providerId = provider ? provider.id : null;

                    if(this[entity].id) {
                        var endpoint = '/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + this[entity].id;

                        axios.patch(endpoint, $.extend({}, {
                            place: place,
                            activity: activity
                        }, this[entity]))
                            .then(function (response) {
                                var rosterBench = _.find(this.rostersBenchs, {id: response.data.id});
                                _.assignIn(rosterBench, response.data);
                                this.clearRosterBench(entity);
                        }.bind(this));
                    } else {
                        axios.post('/admin/accounts/' + this.account.id + '/pipeline/rosterBench', $.extend({}, {
                            place: place,
                            activity: activity
                        }, this[entity]))
                            .then(function (response) {
                                var rosterBench = response.data;
                                this.rostersBenchs.push(rosterBench);
                                this.clearRosterBench(entity);

                                if(response.data.isSMD) {
                                    this.oldSMD = [];
                                    this.oldSMD.push(response.data);
                                }

                                if(response.data.isAMD) {
                                    this.oldAMD = [];
                                    this.oldAMD.push(response.data);
                                }
                            }.bind(this));
                    }
                },

                addCredentialing: function (entity) {
                    if(this[entity].id) {
                        var endpoint = '/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + this[entity].id;

                        axios.patch(endpoint, this[entity])
                            .then(function (response) {
                                var credentialing = _.find(this.rostersBenchs, {id: response.data.id});
                                _.assignIn(credentialing, response.data);
                                this.clearCredentialing(entity);
                        }.bind(this));
                    }
                },

                editRosterBench: function (rosterBench, object) {
                    rosterBench.interview = this.moment(rosterBench.interview);
                    rosterBench.contractIn = this.moment(rosterBench.contractIn);
                    rosterBench.contractOut = this.moment(rosterBench.contractOut);
                    rosterBench.firstShift = this.moment(rosterBench.firstShift);
                    rosterBench.fileToCredentialing = this.moment(rosterBench.fileToCredentialing);
                    rosterBench.privilegeGoal = this.moment(rosterBench.privilegeGoal);
                    rosterBench.appToHospital = this.moment(rosterBench.appToHospital);
                    rosterBench.provisionalPrivilegeStart = this.moment(rosterBench.provisionalPrivilegeStart);

                    _.assignIn(this[object], rosterBench);
                },

                editCredentialing: function (credentialing, object) {
                    credentialing.interview = this.moment(credentialing.interview);
                    credentialing.contractIn = this.moment(credentialing.contractIn);
                    credentialing.contractOut = this.moment(credentialing.contractOut);
                    credentialing.firstShift = this.moment(credentialing.firstShift);
                    credentialing.fileToCredentialing = this.moment(credentialing.fileToCredentialing);
                    credentialing.privilegeGoal = this.moment(credentialing.privilegeGoal);
                    credentialing.appToHospital = this.moment(credentialing.appToHospital);


                    _.assignIn(this[object], credentialing);
                },

                completeCredentialing: function(credentialing) {
                    credentialing.interview = this.moment(credentialing.interview);
                    credentialing.contractIn = this.moment(credentialing.contractIn);
                    credentialing.contractOut = this.moment(credentialing.contractOut);
                    credentialing.firstShift = this.moment(credentialing.firstShift);
                    credentialing.fileToCredentialing = this.moment(credentialing.fileToCredentialing);
                    credentialing.privilegeGoal = this.moment(credentialing.privilegeGoal);
                    credentialing.appToHospital = this.moment(credentialing.appToHospital);
                    credentialing.completed = 1;

                    var endpoint = '/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + credentialing.id + '/complete';

                    axios.patch(endpoint, credentialing)
                        .then(function (response) {
                            
                        }.bind(this));
                },

                setCredentialing: function(credentialing) {
                    credentialing.interview = this.moment(credentialing.interview);
                    credentialing.contractIn = this.moment(credentialing.contractIn);
                    credentialing.contractOut = this.moment(credentialing.contractOut);
                    credentialing.firstShift = this.moment(credentialing.firstShift);
                    credentialing.fileToCredentialing = this.moment(credentialing.fileToCredentialing);
                    credentialing.privilegeGoal = this.moment(credentialing.privilegeGoal);
                    credentialing.appToHospital = this.moment(credentialing.appToHospital);

                    this.credentialing = _.cloneDeep(credentialing);
                    this.toRemove = credentialing;
                },

                removeCredentialing: function () {
                    var endpoint = '/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + this.credentialing.id + '/remove';

                    axios.patch(endpoint, this.credentialing)
                        .then(function (response) {
                            this.rostersBenchs = _.reject(this.rostersBenchs, { 'id': this.credentialing.id });
                            $('#credentialingModal').modal('hide');
                        }.bind(this));
                },

                switchRosterBenchTo: function (rosterBench, place, type) {
                    rosterBench.place = place;
                    
                    if(type !== undefined) {
                      rosterBench.type = type;
                    }
                    
                    rosterBench.interview = this.moment(rosterBench.interview);
                    rosterBench.contractIn = this.moment(rosterBench.contractIn);
                    rosterBench.contractOut = this.moment(rosterBench.contractOut);
                    rosterBench.firstShift = this.moment(rosterBench.firstShift);
                    rosterBench.fileToCredentialing = this.moment(rosterBench.fileToCredentialing);
                    rosterBench.privilegeGoal = this.moment(rosterBench.privilegeGoal);
                    rosterBench.appToHospital = this.moment(rosterBench.appToHospital);
                    rosterBench.provisionalPrivilegeStart = this.moment(rosterBench.provisionalPrivilegeStart);

                    if (place == 'recruiting') {
                        axios.post('/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + rosterBench.id + '/switch', rosterBench)
                        .then(function (response) {
                            this.recruitings.push(response.data);
                            this.rostersBenchs = _.reject(this.rostersBenchs, { 'id': rosterBench.id });
                        }.bind(this));
                    } else {
                        axios.patch('/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + rosterBench.id, rosterBench)
                        .then(function (response) {
                            var newRosterBench = response.data;
                            _.assignIn(rosterBench, newRosterBench);
                        }.bind(this));
                    }
                },

                deleteRosterBench: function (rosterBench) {
                    if (confirm("@lang('Are you sure you want to delete this record?')")) {
                        axios.delete('/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + rosterBench.id)
                            .then(function (response) {
                                this.rostersBenchs = _.reject(this.rostersBenchs, { 'id': rosterBench.id });
                            }.bind(this));
                    }
                },

                deleteDeclining: function (declining) {
                    if (confirm("@lang('Are you sure you want to delete this record?')")) {
                        var isLocum = _.find(this.locums, function(locum){
                             return locum.id == declining.id;
                        });

                        var isRecruiting = _.find(this.recruitings, function(locum){
                             return locum.id == declining.id;
                        });

                        if (isLocum) {
                            axios.delete('/admin/accounts/' + this.account.id + '/pipeline/locum/' + declining.id)
                                .then(function (response) {
                                    this.locums = _.reject(this.locums, { 'id': declining.id });
                                }.bind(this));
                        } else {
                            axios.delete('/admin/accounts/' + this.account.id + '/pipeline/recruiting/' + declining.id)
                                .then(function (response) {
                                    this.recruitings = _.reject(this.recruitings, { 'id': declining.id });
                                }.bind(this));
                        }
                    }
                },


                addRecruiting: function () {
                    var name = this.newRecruiting.name;
                    var provider = _.find(this.providers, {fullName: name});
                    
                    this.newRecruiting.providerId = provider ? provider.id : null;

                    if(this.newRecruiting.id) {
                        var endpoint = '/admin/accounts/' + this.account.id + '/pipeline/recruiting/' + this.newRecruiting.id;

                        axios.patch(endpoint, this.newRecruiting)
                            .then(function (response) {
                                var recruiting = _.find(this.recruitings, {id: response.data.id});
                                _.assignIn(recruiting, response.data);
                                this.clearNewRecruiting();
                        }.bind(this));
                    } else {
                        axios.post('/admin/accounts/' + this.account.id + '/pipeline/recruiting', this.newRecruiting)
                            .then(function (response) {
                                var recruiting = response.data;
                                this.recruitings.push(recruiting);
                                this.clearNewRecruiting();
                            }.bind(this));
                    }
                },

                editRecruiting: function (recruiting) {
                    recruiting.interview = this.moment(recruiting.interview);
                    recruiting.application = this.moment(recruiting.application);
                    recruiting.contractOut = this.moment(recruiting.contractOut);
                    recruiting.declined = this.moment(recruiting.declined);
                    recruiting.contractIn = this.moment(recruiting.contractIn);
                    recruiting.firstShift = this.moment(recruiting.firstShift);

                    _.assignIn(this.newRecruiting, recruiting);
                },

                deleteRecruiting: function (recruiting) {
                    if (confirm("@lang('Are you sure you want to delete this record?')")) {
                        axios.delete('/admin/accounts/' + this.account.id + '/pipeline/recruiting/' + recruiting.id)
                            .then(function (response) {
                                this.recruitings = _.reject(this.recruitings, { 'id': recruiting.id });
                            }.bind(this));
                    }
                },

                currentRecruiting: function (recruiting) {
                    var firstOfMonth = moment().startOf('month');

                    return moment(recruiting.firstShift).isAfter(firstOfMonth);
                },


                addLocum: function () {
                    var name = this.newLocum.name;
                    var provider = _.find(this.providers, {fullName: name});
                    
                    this.newLocum.providerId = provider ? provider.id : null;

                    if(this.newLocum.id) {
                        var endpoint = '/admin/accounts/' + this.account.id + '/pipeline/locum/' + this.newLocum.id;

                        axios.patch(endpoint, this.newLocum)
                            .then(function (response) {
                                var locum = _.find(this.locums, {id: response.data.id});
                                _.assignIn(locum, response.data);
                                this.clearNewLocum();
                        }.bind(this));
                    } else {
                        axios.post('/admin/accounts/' + this.account.id + '/pipeline/locum', this.newLocum)
                            .then(function (response) {
                                var locum = response.data;
                                this.locums.push(locum);
                                this.clearNewLocum();
                            }.bind(this));
                    }
                },

                editLocum: function (locum) {
                    locum.potentialStart = this.moment(locum.potentialStart);
                    locum.startDate = this.moment(locum.startDate);
                    locum.declined = this.moment(locum.declined);
                    locum.application = this.moment(locum.application);
                    locum.interview = this.moment(locum.interview);

                    _.assignIn(this.newLocum, locum);
                },

                deleteLocum: function (locum) {
                    if (confirm("@lang('Are you sure you want to delete this record?')")) {
                        axios.delete('/admin/accounts/' + this.account.id + '/pipeline/locum/' + locum.id)
                            .then(function (response) {
                                this.locums = _.reject(this.locums, { 'id': locum.id });
                            }.bind(this));
                    }
                },


                setDeclining: function (toDecline) {
                    toDecline.interview = this.moment(toDecline.interview);
                    toDecline.application = this.moment(toDecline.application);
                    toDecline.contractOut = this.moment(toDecline.contractOut);
                    toDecline.declined = this.moment(toDecline.declined);
                    toDecline.contractIn = this.moment(toDecline.contractIn);
                    toDecline.firstShift = this.moment(toDecline.firstShift);

                    this.declining = _.cloneDeep(toDecline);
                    this.toDecline = toDecline;
                },

                decline: function () {
                    var endpoint;

                    if (this.declining.instance == 'recruiting') {
                        endpoint = '/admin/accounts/' + this.account.id + '/pipeline/recruiting/' + this.declining.id + '/decline';
                    } else {
                        endpoint = '/admin/accounts/' + this.account.id + '/pipeline/locum/' + this.declining.id + '/decline';
                    }

                    axios.patch(endpoint, this.declining)
                        .then(function (response) {
                            var declined = response.data;
                            _.assignIn(this.toDecline, declined);
                            $('#declineModal').modal('hide');
                        }.bind(this));
                },


                setResigning: function (toResign) {
                    toResign.resigned = this.moment(toResign.resigned);
                    toResign.interview = this.moment(toResign.interview);
                    toResign.contractIn = this.moment(toResign.contractIn);
                    toResign.contractOut = this.moment(toResign.contractOut);
                    toResign.firstShift = this.moment(toResign.firstShift);
                    toResign.lastShift = this.moment(toResign.lastShift);
                    
                    this.resigning = _.cloneDeep(toResign);
                    this.toResign = toResign;
                },

                resign: function () {
                    var endpoint = '/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + this.resigning.id + '/resign';

                    axios.patch(endpoint, this.resigning)
                        .then(function (response) {
                            var resigned = response.data;
                            _.assignIn(this.toResign, resigned);
                            $('#resignModal').modal('hide');
                        }.bind(this));
                },

                roundStep: function (number, step) {
                    if (step == 0) return 0;

                    var factor = 1 / step;

                    return Math.round(number * factor) / factor;
                },

                updateRosterBench: function(roster, type) {
                    roster.interview = this.moment(roster.interview);
                    roster.contractIn = this.moment(roster.contractIn);
                    roster.contractOut = this.moment(roster.contractOut);
                    roster.firstShift = this.moment(roster.firstShift);
                    roster.fileToCredentialing = this.moment(roster.fileToCredentialing);
                    roster.privilegeGoal = this.moment(roster.privilegeGoal);
                    roster.appToHospital = this.moment(roster.appToHospital);
                    roster.provisionalPrivilegeStart = this.moment(roster.provisionalPrivilegeStart);

                    console.log(roster);

                    var endpoint = '/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + roster.id;

                    if (type == 'SMD') {
                        roster.isSMD = !roster.isSMD;

                        if(roster.isSMD) {
                            this.pipeline.medicalDirector = roster.name;
                        } else {
                            this.pipeline.medicalDirector = '';
                        }

                        axios.patch('/admin/accounts/' + this.account.id + '/pipeline', this.pipeline)
                            .then(function (response) {

                            }.bind(this));
                    } 

                    if (type == 'AMD') {
                        roster.isAMD = !roster.isAMD;
                    }

                    if (type == 'Chief') {
                        if (this.activeRosterPhysicians.length == 0) {
                            if (roster.isChief) {
                                this.isChief = true;
                                this.pipeline.medicalDirector = roster.name;
                            } else {
                                this.isChief = false;
                                this.pipeline.medicalDirector = '';
                            }

                            axios.patch('/admin/accounts/' + this.account.id + '/pipeline', this.pipeline)
                            .then(function (response) {

                            }.bind(this));
                        }
                    }

                    roster.type = type;

                    if ( this.oldSMD.length && type == 'SMD') {
                        this.oldSMD[0].isSMD = 0;
                    }
                    
                    if ( this.oldAMD.length && type == 'AMD') {
                        this.oldAMD[0].isAMD = 0;
                    }

                    if ( this.oldChief.length && type == 'Chief') {
                        this.oldChief[0].isChief = 0;
                    }

                    roster.oldAMD = this.oldAMD.length ? this.oldAMD[0].id : '';
                    roster.oldSMD = this.oldSMD.length ? this.oldSMD[0].id : '';
                    roster.oldChief = this.oldChief.length ? this.oldChief[0].id : '';

                    axios.patch(endpoint, roster)
                        .then(function (response) {
                            if(type == 'SMD') {
                                this.oldSMD = [];
                                roster.isSMD = response.data.isSMD;
                                
                                if(roster.isSMD) {
                                    this.oldSMD.push(roster);
                                }
                            } 

                            if (type == 'AMD') {
                                this.oldAMD = [];
                                roster.isAMD = response.data.isAMD;
                                
                                if(roster.isAMD){
                                    this.oldAMD.push(roster);
                                }
                            }

                            if (type == 'Chief') {
                                this.oldChief = [];
                                roster.isChief = response.data.isChief;
                                
                                if(roster.isChief){
                                    this.oldChief.push(roster);
                                }
                            }
                        }.bind(this));
                },

                updateHighLight: function(roster) {
                    roster.interview = this.moment(roster.interview);
                    roster.contractIn = this.moment(roster.contractIn);
                    roster.contractOut = this.moment(roster.contractOut);
                    roster.firstShift = this.moment(roster.firstShift);
                    roster.fileToCredentialing = this.moment(roster.fileToCredentialing);
                    roster.privilegeGoal = this.moment(roster.privilegeGoal);
                    roster.appToHospital = this.moment(roster.appToHospital);
                    roster.provisionalPrivilegeStart = this.moment(roster.provisionalPrivilegeStart);
                    
                    var endpoint = '/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + roster.id;

                    axios.patch(endpoint, roster)
                        .then(function (response) {
                            
                        }.bind(this));
                },

                updateStopLight: function(roster, color) {
                    if (roster.stopLight == color) {
                        roster.stopLight = null;
                    } else {
                        roster.stopLight = color;
                    }

                    roster.interview = this.moment(roster.interview);
                    roster.contractIn = this.moment(roster.contractIn);
                    roster.contractOut = this.moment(roster.contractOut);
                    roster.firstShift = this.moment(roster.firstShift);
                    roster.fileToCredentialing = this.moment(roster.fileToCredentialing);
                    roster.privilegeGoal = this.moment(roster.privilegeGoal);
                    roster.appToHospital = this.moment(roster.appToHospital);
                    roster.provisionalPrivilegeStart = this.moment(roster.provisionalPrivilegeStart);
                    
                    var endpoint = '/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + roster.id;

                    axios.patch(endpoint, roster)
                        .then(function (response) {
                            
                        }.bind(this));
                },

                checkFirstShift: function(roster, e) {
                    if (roster.firstShift == null) {
                        e.preventDefault();
                        roster.signedNotStarted = 0;
                        alert('Projected Start Date Date must be filled');
                    }
                },


                compareFirstShift: function (firstShift) {
                    if(firstShift) {
                        return moment() <= moment(firstShift);
                    }

                    return true;
                },

                updateRecruitingNoc: function(recruiting) {
                    recruiting.interview = this.moment(recruiting.interview);
                    recruiting.application = this.moment(recruiting.application);
                    recruiting.contractOut = this.moment(recruiting.contractOut);
                    recruiting.declined = this.moment(recruiting.declined);
                    recruiting.contractIn = this.moment(recruiting.contractIn);
                    recruiting.firstShift = this.moment(recruiting.firstShift);

                    var endpoint = '/admin/accounts/' + this.account.id + '/pipeline/recruiting/' + recruiting.id;

                    axios.patch(endpoint, recruiting)
                        .then(function (response) {
                            
                        }.bind(this));
                },

                clearRosterBench: function(entity) {
                    switch(entity) {
                        case 'rosterPhysician': this[entity] = {
                            id: null,
                            name: '',
                            hours: '',
                            interview: '',
                            contractOut: '',
                            contractIn: '',
                            firstShift: '',
                            notes: '',
                        };
                        break;

                        case 'rosterApps': this[entity] = {
                            id: null,
                            name: '',
                            hours: '',
                            interview: '',
                            contractOut: '',
                            contractIn: '',
                            firstShift: '',
                            notes: '',
                        };
                        break;


                        case 'benchPhysician': this[entity] = {
                            name: '',
                            hours: '',
                            interview: '',
                            contractOut: '',
                            contractIn: '',
                            firstShift: '',
                            notes: '',
                        };
                        break;

                        case 'benchApps': this[entity] = {
                            name: '',
                            hours: '',
                            interview: '',
                            contractOut: '',
                            contractIn: '',
                            firstShift: '',
                            notes: '',
                        };
                        break;
                    }
                },

                clearCredentialing: function(entity) {
                    switch(entity) {
                        case 'credentialingPhysician': this[entity] = {
                            id: null,
                            name: '',
                            hours: '',
                            fileToCredentialing: '',
                            privilegeGoal: '',
                            appToHospital: '',
                            stage: '',
                            notes: '',
                        };
                        break;

                        case 'credentialingApp': this[entity] = {
                            id: null,
                            name: '',
                            hours: '',
                            fileToCredentialing: '',
                            privilegeGoal: '',
                            appToHospital: '',
                            stage: '',
                            notes: '',
                        };
                        break;
                    }
                },

                clearNewRecruiting: function(entity) {
                    this.newRecruiting = {
                        type: null,
                        name: '',
                        contract: null,
                        interview: '',
                        contractOut: '',
                        contractIn: '',
                        firstShift: '',
                        notes: '',
                    };
                },

                clearNewLocum: function() {
                    this.newLocum = {
                        type: null,
                        name: '',
                        agency: '',
                        potentialStart: '',
                        credentialingNotes: '',
                        shiftsOffered: '',
                        startDate: '',
                        comments: '',
                    };
                },

                switchRecruitingTo: function(recruiting, place) {
                    recruiting.interview = this.moment(recruiting.interview);
                    recruiting.contractIn = this.moment(recruiting.contractIn);
                    recruiting.contractOut = this.moment(recruiting.contractOut);
                    recruiting.firstShift = this.moment(recruiting.firstShift);

                    recruiting.place = place;
                    recruiting.activity = recruiting.type == 'phys' ? 'physician' : 'app';
                    
                    if(!recruiting.contractIn) {
                        alert('Please fill Contract In date before moving to Roster Or Bench.')
                    } else {
                        axios.post('/admin/accounts/' + this.account.id + '/pipeline/recruiting/' + recruiting.id + '/switch', recruiting
                        ).then(function (response) {
                            this.rostersBenchs.push(response.data);
                            this.recruitings = _.reject(this.recruitings, { 'id': recruiting.id });
                        }.bind(this));
                    }
                },

                switchLocumTo: function(locum, place) {
                    locum.interview = this.moment(locum.interview);
                    locum.declined = this.moment(locum.declined);
                    locum.potentialStart = this.moment(locum.potentialStart);
                    locum.application = this.moment(locum.application);
                    locum.startDate = this.moment(locum.startDate);

                    locum.place = place;
                    locum.activity = locum.type == 'phys' ? 'physician' : 'app';

                    axios.post('/admin/accounts/' + this.account.id + '/pipeline/locum/' + locum.id + '/switch', locum
                    ).then(function (response) {
                        this.rostersBenchs.push(response.data);
                        this.locums = _.reject(this.locums, { 'id': locum.id });
                    }.bind(this));
                },

                moment: function(date) {
                    if(date) {
                        return moment(date).format('MM/DD/YYYY');
                    }

                    return null;
                }
            },

            mounted: function () {
                this.pipeline.staffPhysicianFTEHaves = this.staffPhysicianFTEHaves;
                this.pipeline.staffPhysicianFTENeeds = this.staffPhysicianFTENeeds;
                this.pipeline.staffPhysicianFTEOpenings = this.staffPhysicianFTEOpenings;
                this.pipeline.staffPhysicianHaves = this.staffPhysicianHaves;
                this.pipeline.staffPhysicianNeeds = this.staffPhysicianNeeds;
                this.pipeline.staffPhysicianOpenings = this.staffPhysicianOpenings;

                this.pipeline.staffAppsFTEHaves = this.staffAppsFTEHaves;
                this.pipeline.staffAppsFTENeeds = this.staffAppsFTENeeds;
                this.pipeline.staffAppsFTEOpenings = this.staffAppsFTEOpenings;
                this.pipeline.staffAppsHaves = this.staffAppsHaves;
                this.pipeline.staffAppsNeeds = this.staffAppsNeeds;
                this.pipeline.staffAppsOpenings = this.staffAppsOpenings;

                this.pipeline.fullTimeHoursPhys = this.fullTimeHoursPhys;
                this.pipeline.fullTimeHoursApps = this.fullTimeHoursApps;

                axios.patch('/admin/accounts/' + this.account.id + '/pipeline', this.pipeline)
                .then(function (response) {

                }.bind(this));
            }
        });
    </script>
@endpush