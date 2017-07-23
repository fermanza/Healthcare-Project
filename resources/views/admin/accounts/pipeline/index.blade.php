@extends('layouts.admin')

@section('content-header', __('Pipeline'))

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

                        @if ($practice->isIPS())
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

            {{-- @permission('admin.accounts.create') --}}
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-info">
                            Update
                        </button>
                    </div>
                </div>
            {{-- @endpermission --}}

            <hr />

            <h4>@lang('Complete Staffing and Current Openings')</h4>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
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

            {{-- @permission('admin.accounts.create') --}}
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-info">
                            Update
                        </button>
                    </div>
                </div>
            {{-- @endpermission --}}
        </form>

        <h4>@lang('Current Roster')</h4>
        <form @submit.prevent.stop="addRosterBench('roster', 'physician', 'rosterPhysicians', 'rosterPhysician')">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
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
                        <tr v-for="roster in pipeline.rosterPhysicians">
                            <td>@{{ roster.name }}</td>
                            <td>@{{ roster.hours }}</td>
                            <td>@{{ roster.interview }}</td>
                            <td>@{{ roster.contractOut }}</td>
                            <td>@{{ roster.contractIn }}</td>
                            <td>@{{ roster.firstShift }}</td>
                            <td class="text-center">
                                <button @click="deleteRosterBench(roster, 'rosterPhysicians')" type="button" class="btn btn-xs btn-danger">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <input type="text" class="form-control" v-model="rosterPhysician.name" />
                            </td>
                            <td>
                                <input type="number" class="form-control" v-model="rosterPhysician.hours" />
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
                                <button type="submit" class="btn btn-xs btn-success">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </form>

        <form @submit.prevent.stop="addRosterBench('roster', 'app', 'rosterApps', 'rosterApps')">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
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
                        <tr v-for="roster in pipeline.rosterApps">
                            <td>@{{ roster.name }}</td>
                            <td>@{{ roster.hours }}</td>
                            <td>@{{ roster.interview }}</td>
                            <td>@{{ roster.contractOut }}</td>
                            <td>@{{ roster.contractIn }}</td>
                            <td>@{{ roster.firstShift }}</td>
                            <td class="text-center">
                                <button @click="deleteRosterBench(roster, 'rosterApps')" type="button" class="btn btn-xs btn-danger">
                                    <i class="fa fa-trash"></i>
                                </button>
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
                                <input type="text" class="form-control datepicker" v-model="rosterApps.interview" required />
                            </td>
                            <td>
                                <input type="text" class="form-control datepicker" v-model="rosterApps.contractOut" required />
                            </td>
                            <td>
                                <input type="text" class="form-control datepicker" v-model="rosterApps.contractIn" required />
                            </td>
                            <td>
                                <input type="text" class="form-control datepicker" v-model="rosterApps.firstShift" required />
                            </td>
                            <td class="text-center">
                                <button type="submit" class="btn btn-xs btn-success">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </form>
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
            },

            computed: {
                staffPhysicianHaves: function () {
                    return this.practiceTime == 'hours' ? _(this.pipeline.rosterPhysicians).sumBy('hours') : this.pipeline.rosterPhysicians.length;
                },

                staffAppsHaves: function () {
                    return this.practiceTime == 'hours' ? _(this.pipeline.rosterApps).sumBy('hours') : this.pipeline.rosterApps.length;
                },

                staffPhysicianOpenings: function () {
                    return this.staffPhysicianNeeds - this.staffPhysicianHaves;
                },

                staffAppsOpenings: function () {
                    return this.staffAppsNeeds - this.staffAppsHaves;
                },
            },

            methods: {
                deleteRosterBench: function (rosterBench, location) {
                    axios.delete('/admin/accounts/' + this.account.id + '/pipeline/rosterBench/' + rosterBench.id)
                        .then(function (response) {
                            this.pipeline[location] = _.reject(this.pipeline[location], { 'id': rosterBench.id });
                        }.bind(this));
                },

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
            }
        });
    </script>
@endpush
