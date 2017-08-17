@extends('layouts.admin')

@section('content-header', __('Summary'))

@section('tools')
    <a href="javascript:print();" class="btn btn-default btn-sm hidden-print">
        <i class="fa fa-print"></i>
        @lang('Print')
    </a>
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
                            <label for="medicalDirector">@lang('Medical Director'):</label>
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
                        <tbody>
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
                                <th class="text-center">@lang('Hours')</th>
                                <th class="text-center">@lang('FTEs')</th>
                                <th>&nbsp;</th>
                                <th class="text-center">@lang('Hours')</th>
                                <th class="text-center">@lang('FTEs')</th>
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
                                    <input type="text" class="form-control hidden-print" name="staffPhysicianFTEOpenings" value="{{ old('staffPhysicianFTEOpenings') ?: $pipeline->staffPhysicianFTEOpenings }}" v-model="staffPhysicianFTEOpenings" readonly />
                                    <span class="visible-print">@{{ staffPhysicianFTEOpenings }}</span>
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
                        </tbody>
                    </table>
                </div>
            </div>
        </form>


        <hr />


        <div class="no-break-inside">
            <h4 class="roster-title">@lang('Current Roster')</h4>
            <h6 class="pseudo-header bg-gray">@lang('Physician')</h6>
            <form @submit.prevent="addRosterBench('roster', 'physician', 'rosterPhysician')">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-gray">
                            <tr>
                                <th class="mw50">@lang('SMD')</th>
                                <th class="mw50">@lang('AMD')</th>
                                <th class="mw200">@lang('Name')</th>
                                <th class="mw70">@lang('Hours')</th>
                                <th class="mw100">@lang('Interview')</th>
                                <th class="mw100">@lang('Contract Out')</th>
                                <th class="mw100">@lang('Contract In')</th>
                                <th class="mw100">@lang('First Shift')</th>
                                <th class="mw200 w100">@lang('Last Contact Date & Next Steps')</th>
                                <th class="mw150 text-center hidden-print">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="roster in activeRosterPhysicians">
                                <td>
                                    <input class="roster-radio" type="checkbox" name="SMD" :value="roster.name" :checked='roster.isSMD' @change="updateRosterBench(roster, 'SMD')">
                                </td>
                                <td>
                                    <input class="roster-radio" type="checkbox" name="AMD" :value="roster.name" :checked='roster.isAMD' @change="updateRosterBench(roster, 'AMD')">
                                </td>
                                <td>@{{ roster.name }}</td>
                                <td>@{{ roster.hours }}</td>
                                <td>@{{ roster.interview }}</td>
                                <td>@{{ roster.contractOut }}</td>
                                <td>@{{ roster.contractIn }}</td>
                                <td>@{{ roster.firstShift }}</td>
                                <td>@{{ roster.notes }}</td>
                                <td class="text-center hidden-print">
                                    @permission('admin.accounts.pipeline.rosterBench.resign')
                                        <button type="button" class="btn btn-xs btn-warning"
                                            data-toggle="modal" data-target="#resignModal"
                                            @click="setResigning(roster)"
                                        >
                                            @lang('Resign')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.update')
                                        <button type="button" class="btn btn-xs btn-info"
                                            @click="switchRosterBenchTo(roster, 'bench')"
                                        >
                                            @lang('Bench')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.store')
                                        <button type="button" class="btn btn-xs btn-info"
                                            @click="editRosterBench(roster, 'rosterPhysician')"
                                        >
                                            <i class="fa fa-pencil"></i>
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
                                    <input type="text" class="form-control" v-model="rosterPhysician.name" required />
                                </td>
                                <td>
                                    <input type="number" class="form-control" v-model="rosterPhysician.hours" min="0" required />
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
                    <table class="table table-bordered">
                        <thead class="bg-gray">
                            <tr>
                                <th class="mw200">@lang('Name')</th>
                                <th class="mw70">@lang('Hours')</th>
                                <th class="mw100">@lang('Interview')</th>
                                <th class="mw100">@lang('Contract Out')</th>
                                <th class="mw100">@lang('Contract In')</th>
                                <th class="mw100">@lang('First Shift')</th>
                                <th class="mw200 w100">@lang('Last Contact Date & Next Steps')</th>
                                <th class="mw150 text-center hidden-print">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="roster in activeRosterApps">
                                <td>@{{ roster.name }}</td>
                                <td>@{{ roster.hours }}</td>
                                <td>@{{ roster.interview }}</td>
                                <td>@{{ roster.contractOut }}</td>
                                <td>@{{ roster.contractIn }}</td>
                                <td>@{{ roster.firstShift }}</td>
                                <td>@{{ roster.notes }}</td>
                                <td class="text-center hidden-print">
                                    @permission('admin.accounts.pipeline.rosterBench.resign')
                                        <button type="button" class="btn btn-xs btn-warning"
                                            data-toggle="modal" data-target="#resignModal"
                                            @click="setResigning(roster)"
                                        >
                                            @lang('Resign')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.update')
                                        <button type="button" class="btn btn-xs btn-info"
                                            @click="switchRosterBenchTo(roster, 'bench')"
                                        >
                                            @lang('Bench')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.store')
                                        <button type="button" class="btn btn-xs btn-info"
                                            @click="editRosterBench(roster, 'rosterApps')"
                                        >
                                            <i class="fa fa-pencil"></i>
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
                                    <input type="text" class="form-control" v-model="rosterApps.name" required />
                                </td>
                                <td>
                                    <input type="number" class="form-control" v-model="rosterApps.hours" min="0" required />
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
            <h4 class="roster-title">@lang('Current Bench')</h4>
            <h6 class="pseudo-header bg-gray">@lang('Physician')</h6>
            <form @submit.prevent="addRosterBench('bench', 'physician', 'benchPhysician')">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-gray">
                            <tr>
                                <th class="mw200">@lang('Name')</th>
                                <th class="mw70">@lang('Hours')</th>
                                <th class="mw100">@lang('Interview')</th>
                                <th class="mw100">@lang('Contract Out')</th>
                                <th class="mw100">@lang('Contract In')</th>
                                <th class="mw100">@lang('First Shift')</th>
                                <th class="mw200 w100">@lang('Last Contact Date & Next Steps')</th>
                                <th class="mw150 text-center hidden-print">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="bench in activeBenchPhysicians">
                                <td>@{{ bench.name }}</td>
                                <td>@{{ bench.hours }}</td>
                                <td>@{{ bench.interview }}</td>
                                <td>@{{ bench.contractOut }}</td>
                                <td>@{{ bench.contractIn }}</td>
                                <td>@{{ bench.firstShift }}</td>
                                <td>@{{ bench.notes }}</td>
                                <td class="text-center hidden-print">
                                    @permission('admin.accounts.pipeline.rosterBench.resign')
                                        <button type="button" class="btn btn-xs btn-warning"
                                            data-toggle="modal" data-target="#resignModal"
                                            @click="setResigning(bench)"
                                        >
                                            @lang('Resign')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.update')
                                        <button type="button" class="btn btn-xs btn-info"
                                            @click="switchRosterBenchTo(bench, 'roster')"
                                        >
                                            @lang('Roster')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.store')
                                        <button type="button" class="btn btn-xs btn-info"
                                            @click="editRosterBench(bench, 'benchPhysician')"
                                        >
                                            <i class="fa fa-pencil"></i>
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
                                    <input type="text" class="form-control" v-model="benchPhysician.name" required />
                                </td>
                                <td>
                                    <input type="number" class="form-control" v-model="benchPhysician.hours" min="0" required />
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
                    <table class="table table-bordered">
                        <thead class="bg-gray">
                            <tr>
                                <th class="mw200">@lang('Name')</th>
                                <th class="mw70">@lang('Hours')</th>
                                <th class="mw100">@lang('Interview')</th>
                                <th class="mw100">@lang('Contract Out')</th>
                                <th class="mw100">@lang('Contract In')</th>
                                <th class="mw100">@lang('First Shift')</th>
                                <th class="mw200 w100">@lang('Last Contact Date & Next Steps')</th>
                                <th class="mw150 text-center hidden-print">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="bench in activeBenchApps">
                                <td>@{{ bench.name }}</td>
                                <td>@{{ bench.hours }}</td>
                                <td>@{{ bench.interview }}</td>
                                <td>@{{ bench.contractOut }}</td>
                                <td>@{{ bench.contractIn }}</td>
                                <td>@{{ bench.firstShift }}</td>
                                <td>@{{ bench.notes }}</td>
                                <td class="text-center hidden-print">
                                    @permission('admin.accounts.pipeline.rosterBench.resign')
                                        <button type="button" class="btn btn-xs btn-warning"
                                            data-toggle="modal" data-target="#resignModal"
                                            @click="setResigning(bench)"
                                        >
                                            @lang('Resign')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.update')
                                        <button type="button" class="btn btn-xs btn-info"
                                            @click="switchRosterBenchTo(bench, 'roster')"
                                        >
                                            @lang('Roster')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.rosterBench.store')
                                        <button type="button" class="btn btn-xs btn-info"
                                            @click="editRosterBench(bench, 'benchApps')"
                                        >
                                            <i class="fa fa-pencil"></i>
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
                                    <input type="text" class="form-control" v-model="benchApps.name" required />
                                </td>
                                <td>
                                    <input type="number" class="form-control" v-model="benchApps.hours" min="0" required />
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
            <h4>@lang('Recruiting Pipeline')</h4>
            <form @submit.prevent="addRecruiting">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-gray">
                            <tr>
                                <th class="mw60">@lang('MD/APP')</th>
                                <th class="mw200">@lang('Name')</th>
                                <th class="mw60">@lang('FT/PT')</th>
                                <th class="mw100">@lang('Interview')</th>
                                <th class="mw100">@lang('Contract Out')</th>
                                <th class="mw100">@lang('Contract In')</th>
                                <th class="mw100">@lang('First Shift')</th>
                                <th class="mw200 w100">@lang('Last Contact Date & Next Steps')</th>
                                <th class="mw120 text-center hidden-print">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="recruiting in sortedRecruitings" :class="{'bg-success': currentRecruiting(recruiting)}">
                                <td class="text-uppercase">@{{ recruiting.type }}</td>
                                <td>@{{ recruiting.name }}</td>
                                <td class="text-uppercase">@{{ recruiting.contract }}</td>
                                <td>@{{ recruiting.interview }}</td>
                                <td>@{{ recruiting.contractOut }}</td>
                                <td>@{{ recruiting.contractIn }}</td>
                                <td>@{{ recruiting.firstShift }}</td>
                                <td>@{{ recruiting.notes }}</td>
                                <td class="text-center hidden-print">
                                    @permission('admin.accounts.pipeline.recruiting.decline')
                                        <button type="button" class="btn btn-xs btn-warning"
                                            data-toggle="modal" data-target="#declineModal"
                                            @click="setDeclining(recruiting)"
                                        >
                                            @lang('Decline')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.recruiting.store')
                                        <button type="button" class="btn btn-xs btn-info"
                                            @click="editRecruiting(recruiting)"
                                        >
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.recruiting.destroy')
                                        <button @click="deleteRecruiting(recruiting)" type="button" class="btn btn-xs btn-danger">
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
                                    <input type="text" class="form-control" v-model="newRecruiting.name" required />
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
            <h4>@lang('Locums Pipeline')</h4>
            <form @submit.prevent="addLocum">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="bg-gray">
                            <tr>
                                <th class="mw60">@lang('MD/APP')</th>
                                <th class="mw200">@lang('Name')</th>
                                <th class="mw100">@lang('Agency')</th>
                                <th class="mw100">@lang('Potential Start')</th>
                                <th class="mw200 w50">@lang('Credentialing Notes')</th>
                                <th class="mw70">@lang('Shifts')</th>
                                <th class="mw100">@lang('Start Date')</th>
                                <th class="mw200 w50">@lang('Comments')</th>
                                <th class="mw120 text-center hidden-print">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="locum in sortedLocums">
                                <td class="text-uppercase">@{{ locum.type }}</td>
                                <td>@{{ locum.name }}</td>
                                <td>@{{ locum.agency }}</td>
                                <td>@{{ locum.potentialStart }}</td>
                                <td>@{{ locum.credentialingNotes }}</td>
                                <td>@{{ locum.shiftsOffered }}</td>
                                <td>@{{ locum.startDate }}</td>
                                <td>@{{ locum.comments }}</td>
                                <td class="text-center hidden-print">
                                    @permission('admin.accounts.pipeline.locum.decline')
                                        <button type="button" class="btn btn-xs btn-warning"
                                            data-toggle="modal" data-target="#declineModal"
                                            @click="setDeclining(locum)"
                                        >
                                            @lang('Decline')
                                        </button>
                                    @endpermission

                                    @permission('admin.accounts.pipeline.locum.store')
                                        <button type="button" class="btn btn-xs btn-info"
                                            @click="editLocum(locum)"
                                        >
                                            <i class="fa fa-pencil"></i>
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
                                    <input type="text" class="form-control" v-model="newLocum.name" required />
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
            <h4>@lang('Declined List')</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-gray">
                        <tr>
                            <th class="mw200">@lang('Name')</th>
                            <th class="mw60">@lang('FT/PT')</th>
                            <th class="mw100">@lang('Interview')</th>
                            <th class="mw100">@lang('Application')</th>
                            <th class="mw100">@lang('Contract Out')</th>
                            <th class="mw100">@lang('Declined')</th>
                            <th class="mw200 w100">@lang('Reason')</th>
                            <th class="mw50 text-center hidden-print">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="declined in declines">
                            <td>@{{ declined.name }}</td>
                            <td class="text-uppercase">@{{ declined.contract }}</td>
                            <td>@{{ declined.interview }}</td>
                            <td>@{{ declined.application }}</td>
                            <td>@{{ declined.contractOut }}</td>
                            <td>@{{ declined.declined }}</td>
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

        <div class="no-break-inside">
            <h4>@lang('Resigned List')</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-gray">
                        <tr>
                            <th class="mw60">@lang('MD/APP')</th>
                            <th class="mw200">@lang('Name')</th>
                            <th class="mw100">@lang('Resigned')</th>
                            <th class="mw200 w100">@lang('Reason')</th>
                            <th class="mw50 text-center hidden-print">@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="resigned in resigns">
                            <td class="text-uppercase">@{{ resigned.type }}</td>
                            <td>@{{ resigned.name }}</td>
                            <td>@{{ resigned.resigned }}</td>
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

    </div>
@endsection

@push('scripts')
    <script>
        window.app = new Vue({
            el: '#app',

            data: {
                account: BackendVars.account,
                pipeline: BackendVars.pipeline,

                staffPhysicianNeeds: BackendVars.pipeline.staffPhysicianNeeds,
                staffAppsNeeds: BackendVars.pipeline.staffAppsNeeds,

                fullTimeHoursPhys: BackendVars.pipeline.fullTimeHoursPhys,
                fullTimeHoursApps: BackendVars.pipeline.fullTimeHoursApps,

                oldSMD: BackendVars.pipeline.rostersBenchs.filter( function(roster) { return roster.isSMD == 1 } ),
                oldAMD: BackendVars.pipeline.rostersBenchs.filter( function(roster) { return roster.isAMD == 1 } ),

                rosterPhysician: {
                    name: '',
                    hours: '',
                    interview: '',
                    contractOut: '',
                    contractIn: '',
                    firstShift: '',
                    notes: '',
                },

                rosterApps: {
                    name: '',
                    hours: '',
                    interview: '',
                    contractOut: '',
                    contractIn: '',
                    firstShift: '',
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
                },

                benchApps: {
                    name: '',
                    hours: '',
                    interview: '',
                    contractOut: '',
                    contractIn: '',
                    firstShift: '',
                    notes: '',
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


                resigning: {
                    id: null,
                    type: null,
                    resigned: '',
                    resignedReason: '',
                },
                toResign: {},
            },

            computed: {
                staffPhysicianHaves: function () {
                    return this.pipeline.practiceTime == 'hours' ? _(this.activeRosterPhysicians).sumBy('hours') : this.activeRosterPhysicians.length;
                },

                staffAppsHaves: function () {
                    return this.pipeline.practiceTime == 'hours' ? _(this.activeRosterApps).sumBy('hours') : this.activeRosterApps.length;
                },

                staffPhysicianOpenings: function () {
                    return this.staffPhysicianNeeds - this.staffPhysicianHaves;
                },

                staffAppsOpenings: function () {
                    return this.staffAppsNeeds - this.staffAppsHaves;
                },

                staffPhysicianFTEHaves: function () {
                    if (this.pipeline.practiceTime == 'fte') return '';
                    if (this.fullTimeHoursPhys == 0) return 0;
                    var result = this.staffPhysicianHaves / this.fullTimeHoursPhys;
                    
                    return this.roundStep(result, 0.5);
                },

                staffAppsFTEHaves: function () {
                    if (this.pipeline.practiceTime == 'fte') return '';
                    if (this.fullTimeHoursApps == 0) return 0;
                    var result = this.staffAppsHaves / this.fullTimeHoursApps;
                    
                    return this.roundStep(result, 0.5);
                },

                staffPhysicianFTENeeds: function () {
                    if (this.pipeline.practiceTime == 'fte') return '';
                    if (this.fullTimeHoursPhys == 0) return 0;
                    var result = this.staffPhysicianNeeds / this.fullTimeHoursPhys;
                    
                    return this.roundStep(result, 0.5);
                },

                staffAppsFTENeeds: function () {
                    if (this.pipeline.practiceTime == 'fte') return '';
                    if (this.fullTimeHoursApps == 0) return 0;
                    var result = this.staffAppsNeeds / this.fullTimeHoursApps;
                    
                    return this.roundStep(result, 0.5);
                },

                staffPhysicianFTEOpenings: function () {
                    if (this.pipeline.practiceTime == 'fte') return '';
                    if (this.fullTimeHoursPhys == 0) return 0;
                    var result = this.staffPhysicianOpenings / this.fullTimeHoursPhys;
                    
                    return this.roundStep(result, 0.5);
                },

                staffAppsFTEOpenings: function () {
                    if (this.pipeline.practiceTime == 'fte') return '';
                    if (this.fullTimeHoursApps == 0) return 0;
                    var result = this.staffAppsOpenings / this.fullTimeHoursApps;
                    
                    return this.roundStep(result, 0.5);
                },

                activeRosterPhysicians: function () {
                    return _.chain(this.pipeline.rostersBenchs)
                        .filter({ place: 'roster', activity: 'physician' })
                        .reject('resigned')
                        .orderBy(['isSMD', 'isAMD', 'name'], ['desc', 'desc', 'asc'])
                        .value();
                },

                activeRosterApps: function () {
                    return _.chain(this.pipeline.rostersBenchs)
                        .filter({ place: 'roster', activity: 'app' })
                        .reject('resigned')
                        .value();
                },

                activeBenchPhysicians: function () {
                    return _.chain(this.pipeline.rostersBenchs)
                        .filter({ place: 'bench', activity: 'physician' })
                        .reject('resigned')
                        .value();
                },

                activeBenchApps: function () {
                    return _.chain(this.pipeline.rostersBenchs)
                        .filter({ place: 'bench', activity: 'app' })
                        .reject('resigned')
                        .value();
                },

                sortedRecruitings: function () {
                    return _.chain(this.pipeline.recruitings).reject('declined')
                        .orderBy(['type', function (recruiting) {
                            return recruiting.name.toLowerCase();
                        }], ['desc', 'asc']).value();
                },

                sortedLocums: function () {
                    return _.chain(this.pipeline.locums).reject('declined')
                        .orderBy(['type', function (locum) {
                            return locum.name.toLowerCase();
                        }], ['desc', 'asc']).value();
                },

                declines: function () {
                    return _.chain(this.pipeline.recruitings)
                        .concat(this.pipeline.locums)
                        .filter('declined').value();
                },

                resigns: function () {
                    return _.filter(this.pipeline.rostersBenchs, 'resigned');
                },
            },

            methods: {
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

                    axios.post('/admin/accounts/' + this.account.id + '/pipeline/rosterBench', $.extend({}, {
                        place: place,
                        activity: activity
                    }, this[entity]))
                        .then(function (response) {
                            var rosterBench = response.data;
                            this.pipeline.rostersBenchs.push(rosterBench);
                            this[entity] = {};

                            if(response.data.isSMD) {
                                this.oldSMD = [];
                                this.oldSMD.push(response.data);
                            }

                            if(response.data.isAMD) {
                                this.oldAMD = [];
                                this.oldAMD.push(response.data);
                            }
                        }.bind(this));
                },

                editRosterBench: function (rosterBench, object) {
                    axios.delete('/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + rosterBench.id)
                        .then(function (response) {
                            _.assignIn(this[object], rosterBench);
                            this.pipeline.rostersBenchs = _.reject(this.pipeline.rostersBenchs, { 'id': rosterBench.id });
                        }.bind(this));
                },

                switchRosterBenchTo: function (rosterBench, place) {
                    axios.patch('/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + rosterBench.id, {
                        place: place
                    })
                        .then(function (response) {
                            var newRosterBench = response.data;
                            _.assignIn(rosterBench, newRosterBench);
                        }.bind(this));
                },

                deleteRosterBench: function (rosterBench) {
                    if (confirm("@lang('Are you sure you want to delete this record?')")) {
                        axios.delete('/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + rosterBench.id)
                            .then(function (response) {
                                this.pipeline.rostersBenchs = _.reject(this.pipeline.rostersBenchs, { 'id': rosterBench.id });
                            }.bind(this));
                    }
                },


                addRecruiting: function () {
                    axios.post('/admin/accounts/' + this.account.id + '/pipeline/recruiting', this.newRecruiting)
                        .then(function (response) {
                            var recruiting = response.data;
                            this.pipeline.recruitings.push(recruiting);
                            this.newRecruiting = {};
                        }.bind(this));
                },

                editRecruiting: function (recruiting) {
                    axios.delete('/admin/accounts/' + this.account.id + '/pipeline/recruiting/' + recruiting.id)
                        .then(function (response) {
                            _.assignIn(this.newRecruiting, recruiting);
                            this.pipeline.recruitings = _.reject(this.pipeline.recruitings, { 'id': recruiting.id });
                        }.bind(this));
                },

                deleteRecruiting: function (recruiting) {
                    if (confirm("@lang('Are you sure you want to delete this record?')")) {
                        axios.delete('/admin/accounts/' + this.account.id + '/pipeline/recruiting/' + recruiting.id)
                            .then(function (response) {
                                this.pipeline.recruitings = _.reject(this.pipeline.recruitings, { 'id': recruiting.id });
                            }.bind(this));
                    }
                },

                currentRecruiting: function (recruiting) {
                    var firstOfMonth = moment().startOf('month');

                    return moment(recruiting.firstShift).isAfter(firstOfMonth);
                },


                addLocum: function () {
                    axios.post('/admin/accounts/' + this.account.id + '/pipeline/locum', this.newLocum)
                        .then(function (response) {
                            var locum = response.data;
                            this.pipeline.locums.push(locum);
                            this.newLocum = {};
                        }.bind(this));
                },

                editLocum: function (locum) {
                    axios.delete('/admin/accounts/' + this.account.id + '/pipeline/locum/' + locum.id)
                        .then(function (response) {
                            _.assignIn(this.newLocum, locum);
                            this.pipeline.locums = _.reject(this.pipeline.locums, { 'id': locum.id });
                        }.bind(this));
                },

                deleteLocum: function (locum) {
                    if (confirm("@lang('Are you sure you want to delete this record?')")) {
                        axios.delete('/admin/accounts/' + this.account.id + '/pipeline/locum/' + locum.id)
                            .then(function (response) {
                                this.pipeline.locums = _.reject(this.pipeline.locums, { 'id': locum.id });
                            }.bind(this));
                    }
                },


                setDeclining: function (toDecline) {
                    this.declining = _.cloneDeep(toDecline);
                    this.toDecline = toDecline;
                },

                decline: function () {
                    let endpoint;

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
                    var endpoint = '/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + roster.id;

                    roster.type = type;

                    if( this.oldSMD.length && type == 'SMD') {
                        this.oldSMD[0].isSMD = 0;
                    }
                    
                    if( this.oldAMD.length && type == 'AMD') {
                        this.oldAMD[0].isAMD = 0;
                    }

                    roster.oldAMD = this.oldAMD.length ? this.oldAMD[0].id : '';
                    roster.oldSMD = this.oldSMD.length ? this.oldSMD[0].id : '';



                    axios.patch(endpoint, roster)
                        .then(function (response) {
                            if(type == 'SMD') {
                                this.oldSMD = [];
                                roster.isSMD = true;
                                this.oldSMD.push(roster);
                            } else {
                                this.oldAMD = [];
                                roster.isAMD = true;
                                this.oldAMD.push(roster);
                            }
                        }.bind(this));
                }
            }
        });
    </script>
@endpush
