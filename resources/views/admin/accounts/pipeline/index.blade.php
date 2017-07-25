@extends('layouts.admin')

@section('content-header', __('Summary'))

@section('content')
    <div id="app" class="pipeline"{{--  style="min-width: 1000px;" --}}>
        <form class="form-inline" action="{{ route('admin.accounts.pipeline.update', [$account]) }}" method="POST">
            {{ csrf_field() }}
            {{ method_field('PATCH') }}

            <div class="row">
                <div class="col-md-12 text-center lead">
                    {{ $account->name }}, {{ $account->siteCode }}
                    <br />
                    {{ $account->googleAddress }}
                    <br />
                    {{ $account->recruiter ? ($account->recruiter->fullName().', ') : '' }}
                    {{ ($account->recruiter && $account->recruiter->manager) ? $account->recruiter->manager->fullName() : '' }}
                </div>
            </div>

            <hr />

            <div class="row">
                <div class="col-sm-4">
                    <dl class="dl-horizontal">
                        <dt>
                            <div class="form-group{{ $errors->has('medicalDirector') ? ' has-error' : '' }}">
                                <label for="medicalDirector">@lang('Medical Director'):</label>
                            </div>
                        </dt>
                        <dd>
                            <div class="form-group{{ $errors->has('medicalDirector') ? ' has-error' : '' }}">
                                <input type="text" class="form-control" id="medicalDirector" name="medicalDirector" value="{{ old('medicalDirector') ?: $pipeline->medicalDirector }}" />
                                @if ($errors->has('medicalDirector'))
                                    <span class="help-block"><strong>{{ $errors->first('medicalDirector') }}</strong></span>
                                @endif
                            </div>
                        </dd>

                        <dt>
                            <div class="form-group{{ $errors->has('svp') ? ' has-error' : '' }}">
                                <label for="svp">@lang('SVP'):</label>
                            </div>
                        </dt>
                        <dd>
                            <div class="form-group{{ $errors->has('svp') ? ' has-error' : '' }}">
                                <input type="text" class="form-control" id="svp" name="svp" value="{{ old('svp') ?: $pipeline->svp }}" />
                                @if ($errors->has('svp'))
                                    <span class="help-block"><strong>{{ $errors->first('svp') }}</strong></span>
                                @endif
                            </div>
                        </dd>

                        <dt>
                            <div class="form-group{{ $errors->has('practice') ? ' has-error' : '' }}">
                                <label for="practice">@lang('Practice'):</label>
                            </div>
                        </dt>
                        <dd>
                            <input type="text" class="form-control" id="practice" name="practice" value="{{ $practice ? $practice->name : '' }}" disabled />
                        </dd>
                    </dl>
                </div>
                <div class="col-sm-4">
                    <dl class="dl-horizontal">
                        <dt>
                            <div class="form-group{{ $errors->has('rmd') ? ' has-error' : '' }}">
                                <label for="rmd">@lang('RMD'):</label>
                            </div>
                        </dt>
                        <dd>
                            <div class="form-group{{ $errors->has('rmd') ? ' has-error' : '' }}">
                                <input type="text" class="form-control" id="rmd" name="rmd" value="{{ old('rmd') ?: $pipeline->rmd }}" />
                                @if ($errors->has('rmd'))
                                    <span class="help-block"><strong>{{ $errors->first('rmd') }}</strong></span>
                                @endif
                            </div>
                        </dd>

                        <dt>
                            <div class="form-group{{ $errors->has('dca') ? ' has-error' : '' }}">
                                <label for="dca">@lang('DCA'):</label>
                            </div>
                        </dt>
                        <dd>
                            <div class="form-group{{ $errors->has('dca') ? ' has-error' : '' }}">
                                <input type="text" class="form-control" id="dca" name="dca" value="{{ old('dca') ?: $pipeline->dca }}" />
                                @if ($errors->has('dca'))
                                    <span class="help-block"><strong>{{ $errors->first('dca') }}</strong></span>
                                @endif
                            </div>
                        </dd>

                        @if ($practice && $practice->isIPS())
                            <dt>
                                <div class="form-group{{ $errors->has('practiceTime') ? ' has-error' : '' }}">
                                    <label for="practiceTime">@lang('Practice Time'):</label>
                                </div>
                            </dt>
                            <dd>
                                <div class="form-group{{ $errors->has('practiceTime') ? ' has-error' : '' }}">
                                    <select style="width: 154px;" data-width="154px" class="form-control" id="practiceTime" name="practiceTime" v-model="practiceTime">
                                        {{-- <option value="" disabled selected></option> --}}
                                        @foreach ($practiceTimes as $name => $practiceTime)
                                            <option value="{{ $practiceTime }}" {{ (old('practiceTime') == $practiceTime ?: ($pipeline->practiceTime == $practiceTime)) ? 'selected': '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('practiceTime'))
                                        <span class="help-block"><strong>{{ $errors->first('practiceTime') }}</strong></span>
                                    @endif
                                </div>
                            </dd>
                        @else
                            <input type="hidden" name="practiceTime" value="hours" />
                        @endif
                    </dl>
                </div>
                <div class="col-sm-4">
                    <dl class="dl-horizontal">
                        <dt>
                            <div class="form-group{{ $errors->has('rsc') ? ' has-error' : '' }}">
                                <label for="rsc">@lang('RSC'):</label>
                            </div>
                        </dt>
                        <dd>
                            <input type="text" class="form-control" id="rsc" name="rsc" value="{{ $account->rsc ? $account->rsc->name : '' }}" disabled />
                        </dd>

                        <dt>
                            <div class="form-group{{ $errors->has('region') ? ' has-error' : '' }}">
                                <label for="region">@lang('Operating Unit'):</label>
                            </div>
                        </dt>
                        <dd>
                            <input type="text" class="form-control" id="region" name="region" value="{{ $region ? $region->name : '' }}" disabled />
                        </dd>
                    </dl>
                </div>
            </div>

            @permission('admin.accounts.pipeline.update')
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-info">
                            Update
                        </button>
                    </div>
                </div>
            @endpermission

            <hr />

            <h4>@lang('Complete Staffing and Current Openings')</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-gray">
                        <tr>
                            <th colspan="2" class="text-center">@lang('Physician')</th>
                            <th colspan="2" class="text-center">@lang('APPs')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td width="25%">@lang('Haves')</td>
                            <td width="25%">
                                <input type="text" class="form-control" name="staffPhysicianHaves" value="{{ old('staffPhysicianHaves') ?: $pipeline->staffPhysicianHaves }}" v-model="staffPhysicianHaves" readonly />
                            </td>
                            <td width="25%">@lang('Haves')</td>
                            <td width="25%">
                                <input type="text" class="form-control" name="staffAppsHaves" value="{{ old('staffAppsHaves') ?: $pipeline->staffAppsHaves }}" v-model="staffAppsHaves" readonly />
                            </td>
                        </tr>
                        <tr>
                            <td width="25%">@lang('Needs')</td>
                            <td width="25%">
                                <input type="text" class="form-control" name="staffPhysicianNeeds" value="{{ old('staffPhysicianNeeds') ?: $pipeline->staffPhysicianNeeds }}" v-model="staffPhysicianNeeds" />
                            </td>
                            <td width="25%">@lang('Needs')</td>
                            <td width="25%">
                                <input type="text" class="form-control" name="staffAppsNeeds" value="{{ old('staffAppsNeeds') ?: $pipeline->staffAppsNeeds }}" v-model="staffAppsNeeds" />
                            </td>
                        </tr>
                        <tr>
                            <td width="25%">@lang('Openings')</td>
                            <td width="25%">
                                <input type="text" class="form-control" name="staffPhysicianOpenings" value="{{ old('staffPhysicianOpenings') ?: $pipeline->staffPhysicianOpenings }}" v-model="staffPhysicianOpenings" readonly />
                            </td>
                            <td width="25%">@lang('Openings')</td>
                            <td width="25%">
                                <input type="text" class="form-control" name="staffAppsOpenings" value="{{ old('staffAppsOpenings') ?: $pipeline->staffAppsOpenings }}" v-model="staffAppsOpenings" readonly />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @permission('admin.accounts.pipeline.update')
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-info">
                            Update
                        </button>
                    </div>
                </div>
            @endpermission
        </form>


        <hr />


        <h4>@lang('Current Roster')</h4>
        <form @submit.prevent="addRosterBench('roster', 'physician', 'rosterPhysicians', 'rosterPhysician')">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-gray">
                        <tr>
                            <th colspan="7" class="text-center">@lang('Physician')</th>
                        </tr>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Hours')</th>
                            <th>@lang('Interview')</th>
                            <th>@lang('Contract Out')</th>
                            <th>@lang('Contract In')</th>
                            <th>@lang('First Shift')</th>
                            <th>@lang('Actions')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="roster in activeRosterPhysicians">
                            <td>@{{ roster.name }}</td>
                            <td>@{{ roster.hours }}</td>
                            <td>@{{ roster.interview }}</td>
                            <td>@{{ roster.contractOut }}</td>
                            <td>@{{ roster.contractIn }}</td>
                            <td>@{{ roster.firstShift }}</td>
                            <td class="text-center">
                                @permission('admin.accounts.pipeline.rosterBench.resign')
                                    <button type="button" class="btn btn-xs btn-warning"
                                        data-toggle="modal" data-target="#resignModal"
                                        @click="setResigning(roster)"
                                    >
                                        @lang('Resign')
                                    </button>
                                @endpermission
                                
                                @permission('admin.accounts.pipeline.rosterBench.destroy')
                                    <button @click="deleteRosterBench(roster, 'rosterPhysicians')" type="button" class="btn btn-xs btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @endpermission
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <input type="text" class="form-control" v-model="rosterPhysician.name" required />
                            </td>
                            <td>
                                <input type="number" class="form-control" v-model="rosterPhysician.hours" required />
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

        <form @submit.prevent="addRosterBench('roster', 'app', 'rosterApps', 'rosterApps')">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-gray">
                        <tr>
                            <th colspan="7" class="text-center">@lang('APPs')</th>
                        </tr>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Hours')</th>
                            <th>@lang('Interview')</th>
                            <th>@lang('Contract Out')</th>
                            <th>@lang('Contract In')</th>
                            <th>@lang('First Shift')</th>
                            <th>@lang('Actions')</th>
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
                            <td class="text-center">
                                @permission('admin.accounts.pipeline.rosterBench.resign')
                                    <button type="button" class="btn btn-xs btn-warning"
                                        data-toggle="modal" data-target="#resignModal"
                                        @click="setResigning(roster)"
                                    >
                                        @lang('Resign')
                                    </button>
                                @endpermission

                                @permission('admin.accounts.pipeline.rosterBench.destroy')
                                    <button @click="deleteRosterBench(roster, 'rosterApps')" type="button" class="btn btn-xs btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @endpermission
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <input type="text" class="form-control" v-model="rosterApps.name" required />
                            </td>
                            <td>
                                <input type="number" class="form-control" v-model="rosterApps.hours" required />
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


        <hr />


        <h4>@lang('Current Bench')</h4>
        <form @submit.prevent="addRosterBench('bench', 'physician', 'benchPhysicians', 'benchPhysician')">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-gray">
                        <tr>
                            <th colspan="7" class="text-center">@lang('Physician')</th>
                        </tr>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Hours')</th>
                            <th>@lang('Interview')</th>
                            <th>@lang('Contract Out')</th>
                            <th>@lang('Contract In')</th>
                            <th>@lang('First Shift')</th>
                            <th>@lang('Actions')</th>
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
                            <td class="text-center">
                                @permission('admin.accounts.pipeline.rosterBench.resign')
                                    <button type="button" class="btn btn-xs btn-warning"
                                        data-toggle="modal" data-target="#resignModal"
                                        @click="setResigning(bench)"
                                    >
                                        @lang('Resign')
                                    </button>
                                @endpermission

                                @permission('admin.accounts.pipeline.rosterBench.destroy')
                                    <button @click="deleteRosterBench(bench, 'benchPhysicians')" type="button" class="btn btn-xs btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @endpermission
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <input type="text" class="form-control" v-model="benchPhysician.name" required />
                            </td>
                            <td>
                                <input type="number" class="form-control" v-model="benchPhysician.hours" required />
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

        <form @submit.prevent="addRosterBench('bench', 'app', 'benchApps', 'benchApps')">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-gray">
                        <tr>
                            <th colspan="7" class="text-center">@lang('APPs')</th>
                        </tr>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Hours')</th>
                            <th>@lang('Interview')</th>
                            <th>@lang('Contract Out')</th>
                            <th>@lang('Contract In')</th>
                            <th>@lang('First Shift')</th>
                            <th>@lang('Actions')</th>
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
                            <td class="text-center">
                                @permission('admin.accounts.pipeline.rosterBench.resign')
                                    <button type="button" class="btn btn-xs btn-warning"
                                        data-toggle="modal" data-target="#resignModal"
                                        @click="setResigning(bench)"
                                    >
                                        @lang('Resign')
                                    </button>
                                @endpermission

                                @permission('admin.accounts.pipeline.rosterBench.destroy')
                                    <button @click="deleteRosterBench(bench, 'benchApps')" type="button" class="btn btn-xs btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                @endpermission
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <input type="text" class="form-control" v-model="benchApps.name" required />
                            </td>
                            <td>
                                <input type="number" class="form-control" v-model="benchApps.hours" required />
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


        <hr />


        <h4>@lang('Recruiting Pipeline')</h4>
        <form @submit.prevent="addRecruiting">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-gray">
                        <tr>
                            <th>@lang('MD/APP')</th>
                            <th>@lang('Name')</th>
                            <th>@lang('FT/PT')</th>
                            <th>@lang('Interview')</th>
                            <th>@lang('Contract Out')</th>
                            <th>@lang('Contract In')</th>
                            <th>@lang('First Shift')</th>
                            <th>@lang('Notes')</th>
                            <th>@lang('Actions')</th>
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
                            <td class="text-center">
                                @permission('admin.accounts.pipeline.recruiting.decline')
                                    <button type="button" class="btn btn-xs btn-warning"
                                        data-toggle="modal" data-target="#declineModal"
                                        @click="setDeclining(recruiting, 'recruiting')"
                                    >
                                        @lang('Decline')
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
                    <tfoot>
                        <tr>
                            <td>
                                <select style="width: 154px;" data-width="154px" class="form-control" v-model="newRecruiting.type" required>
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
                                <select style="width: 154px;" data-width="154px" class="form-control" v-model="newRecruiting.contract" required>
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


        <hr />


        <h4>@lang('Locums Pipeline')</h4>
        <form @submit.prevent="addLocum">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-gray">
                        <tr>
                            <th>@lang('MD/APP')</th>
                            <th>@lang('Name')</th>
                            <th>@lang('Agency')</th>
                            <th>@lang('Potential Start')</th>
                            <th>@lang('Credentialing Notes')</th>
                            <th>@lang('Shifts Offered')</th>
                            <th>@lang('Start Date')</th>
                            <th>@lang('Comments')</th>
                            <th>@lang('Actions')</th>
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
                            <td class="text-center">
                                @permission('admin.accounts.pipeline.locum.decline')
                                    <button type="button" class="btn btn-xs btn-warning"
                                        data-toggle="modal" data-target="#declineModal"
                                        @click="setDeclining(locum, 'locum')"
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
                    <tfoot>
                        <tr>
                            <td>
                                <select style="width: 154px;" data-width="154px" class="form-control" v-model="newLocum.type" required>
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
                                <input type="number" class="form-control" v-model="newLocum.shiftsOffered" />
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


        <hr />


        <h4>@lang('Declined List')</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="bg-gray">
                    <tr>
                        <th>@lang('Name')</th>
                        <th>@lang('FT/PT')</th>
                        <th>@lang('Interview')</th>
                        <th>@lang('Application')</th>
                        <th>@lang('Contract Out')</th>
                        <th>@lang('Declined')</th>
                        <th>@lang('Reason')</th>
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
                    </tr>
                </tbody>
            </table>
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
                            <div class="row">
                                <div class="col-xs-6">
                                    <button type="submit" class="btn btn-warning">
                                        @lang('Decline')
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

        <h4>@lang('Resigned List')</h4>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="bg-gray">
                    <tr>
                        <th>@lang('MD/APP')</th>
                        <th>@lang('Name')</th>
                        <th>@lang('Resigned')</th>
                        <th>@lang('Reason')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="resigned in resigns">
                        <td class="text-uppercase">@{{ resigned.type }}</td>
                        <td>@{{ resigned.name }}</td>
                        <td>@{{ resigned.resigned }}</td>
                        <td>@{{ resigned.resignedReason }}</td>
                    </tr>
                </tbody>
            </table>
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
                            <div class="row">
                                <div class="col-xs-6">
                                    <button type="submit" class="btn btn-warning">
                                        @lang('Resign')
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

                practiceTime: BackendVars.pipeline.practiceTime,

                staffPhysicianNeeds: BackendVars.pipeline.staffPhysicianNeeds,
                staffAppsNeeds: BackendVars.pipeline.staffAppsNeeds,

                rosterPhysician: {
                    name: '',
                    hours: '',
                    interview: '',
                    contractOut: '',
                    contractIn: '',
                    firstShift: '',
                },

                rosterApps: {
                    name: '',
                    hours: '',
                    interview: '',
                    contractOut: '',
                    contractIn: '',
                    firstShift: '',
                },


                benchPhysician: {
                    name: '',
                    hours: '',
                    interview: '',
                    contractOut: '',
                    contractIn: '',
                    firstShift: '',
                },

                benchApps: {
                    name: '',
                    hours: '',
                    interview: '',
                    contractOut: '',
                    contractIn: '',
                    firstShift: '',
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
                },
                decliningType: '',
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
                    return this.practiceTime == 'hours' ? _(this.activeRosterPhysicians).sumBy('hours') : this.activeRosterPhysicians.length;
                },

                staffAppsHaves: function () {
                    return this.practiceTime == 'hours' ? _(this.activeRosterApps).sumBy('hours') : this.activeRosterApps.length;
                },

                staffPhysicianOpenings: function () {
                    return this.staffPhysicianNeeds - this.staffPhysicianHaves;
                },

                staffAppsOpenings: function () {
                    return this.staffAppsNeeds - this.staffAppsHaves;
                },

                activeRosterPhysicians: function () {
                    return _.reject(this.pipeline.rosterPhysicians, 'resigned');
                },

                activeRosterApps: function () {
                    return _.reject(this.pipeline.rosterApps, 'resigned');
                },

                activeBenchPhysicians: function () {
                    return _.reject(this.pipeline.benchPhysicians, 'resigned');
                },

                activeBenchApps: function () {
                    return _.reject(this.pipeline.benchApps, 'resigned');
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
                    return _.chain(this.pipeline.rosterPhysicians)
                        .concat(this.pipeline.rosterApps)
                        .concat(this.pipeline.benchPhysicians)
                        .concat(this.pipeline.benchApps)
                        .filter('resigned').value();
                },
            },

            methods: {
                addRosterBench: function (place, activity, location, entity) {
                    axios.post('/admin/accounts/' + this.account.id + '/pipeline/rosterBench', $.extend({}, {
                        place: place,
                        activity: activity
                    }, this[entity]))
                        .then(function (response) {
                            const rosterBench = response.data;
                            this.pipeline[location].push(rosterBench);
                            this[entity] = {};
                        }.bind(this));
                },

                deleteRosterBench: function (rosterBench, location) {
                    axios.delete('/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + rosterBench.id)
                        .then(function (response) {
                            this.pipeline[location] = _.reject(this.pipeline[location], { 'id': rosterBench.id });
                        }.bind(this));
                },


                addRecruiting: function () {
                    axios.post('/admin/accounts/' + this.account.id + '/pipeline/recruiting', this.newRecruiting)
                        .then(function (response) {
                            const recruiting = response.data;
                            this.pipeline.recruitings.push(recruiting);
                            this.newRecruiting = {};
                        }.bind(this));
                },

                deleteRecruiting: function (recruiting) {
                    axios.delete('/admin/accounts/' + this.account.id + '/pipeline/recruiting/' + recruiting.id)
                        .then(function (response) {
                            this.pipeline.recruitings = _.reject(this.pipeline.recruitings, { 'id': recruiting.id });
                        }.bind(this));
                },

                currentRecruiting: function (recruiting) {
                    const firstOfMonth = moment().startOf('month');

                    return moment(recruiting.firstShift).isAfter(firstOfMonth);
                },


                addLocum: function () {
                    axios.post('/admin/accounts/' + this.account.id + '/pipeline/locum', this.newLocum)
                        .then(function (response) {
                            const locum = response.data;
                            this.pipeline.locums.push(locum);
                            this.newLocum = {};
                        }.bind(this));
                },

                deleteLocum: function (locum) {
                    axios.delete('/admin/accounts/' + this.account.id + '/pipeline/locum/' + locum.id)
                        .then(function (response) {
                            this.pipeline.locums = _.reject(this.pipeline.locums, { 'id': locum.id });
                        }.bind(this));
                },


                setDeclining: function (toDecline, type) {
                    this.declining = _.cloneDeep(toDecline);
                    this.decliningType = type;
                    this.toDecline = toDecline;
                },

                decline: function () {
                    let endpoint;

                    if (this.decliningType == 'recruiting') {
                        endpoint = '/admin/accounts/' + this.account.id + '/pipeline/recruiting/' + this.declining.id + '/decline';
                    } else {
                        endpoint = '/admin/accounts/' + this.account.id + '/pipeline/locum/' + this.declining.id + '/decline';
                    }

                    axios.patch(endpoint, this.declining)
                        .then(function (response) {
                            const declined = response.data;
                            _.assignIn(this.toDecline, declined);
                            $('#declineModal').modal('hide');
                        }.bind(this));
                },


                setResigning: function (toResign) {
                    this.resigning = _.cloneDeep(toResign);
                    this.toResign = toResign;
                },

                resign: function () {
                    const endpoint = '/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + this.resigning.id + '/resign';

                    axios.patch(endpoint, this.resigning)
                        .then(function (response) {
                            const resigned = response.data;
                            _.assignIn(this.toResign, resigned);
                            $('#resignModal').modal('hide');
                        }.bind(this));
                },
            }
        });
    </script>
@endpush
