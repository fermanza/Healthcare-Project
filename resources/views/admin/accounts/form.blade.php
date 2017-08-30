@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.accounts.store') : route('admin.accounts.update', [$account]) }}" method="POST">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-12 text-right">
            @include('admin.common.submit', [
                'action' => $action,
                'store' => 'admin.accounts.store',
                'update' => 'admin.accounts.update'
            ])
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name">@lang('Name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?: $account->name }}" required />
                @if ($errors->has('name'))
                    <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('siteCode') ? ' has-error' : '' }}">
                <label for="siteCode">@lang('Site Code')</label>
                @if ($action == 'edit')
                    <small>
                        <a href="javascript:;" data-toggle="modal" data-target="#site-code-history">
                            @lang('History')
                        </a>
                    </small>
                @endif
                <input type="text" class="form-control" id="siteCode" name="siteCode" value="{{ old('siteCode') ?: $account->siteCode }}" required />
                @if ($errors->has('siteCode'))
                    <span class="help-block"><strong>{{ $errors->first('siteCode') }}</strong></span>
                @endif
            </div>
        </div>
    </div>

    <hr />

    <div class="row">
        <div class="col-md-6 text-center">
            <div class="image-upload mt15" 
                data-upload-path="/admin/accounts/image" 
                data-current-path="{{ old('photoPath') ?: $account->photoPath ?: '/img/upload-placeholder.png' }}" 
                data-success="updatePathInput"
            >
            </div>
            <input type="hidden" id="photoPath" name="photoPath" value="{{ old('photoPath') ?: $account->photoPath }}" />
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('googleAddress') ? ' has-error' : '' }}">
                <label for="googleAddress">@lang('Address')</label>
                <input type="text" class="form-control" id="googleAddress" name="googleAddress" value="{{ old('googleAddress') ?: $account->googleAddress }}" />
                @if ($errors->has('googleAddress'))
                    <span class="help-block"><strong>{{ $errors->first('googleAddress') }}</strong></span>
                @endif
            </div>
            <div class="text-center">
                <div id="map"></div>
            </div>
        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-3">
            <div class="form-group{{ $errors->has('recruiterId') ? ' has-error' : '' }}">
                <label for="recruiterId">@lang('Recruiter')</label>
                <select class="form-control select2" id="recruiterId" name="recruiterId">
                    <option value="" disabled selected></option>
                    @foreach ($recruiters as $recruiter)
                        <option value="{{ $recruiter->id }}" {{ (old('recruiterId') == $recruiter->id ?: ($account->recruiter && $account->recruiter->recruiterId == $recruiter->id)) ? 'selected': '' }}>{{ $recruiter->fullName() }}</option>
                    @endforeach
                </select>
                @if ($errors->has('recruiterId'))
                    <span class="help-block"><strong>{{ $errors->first('recruiterId') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group{{ $errors->has('recruiters') ? ' has-error' : '' }}">
                <label for="recruiters[]">@lang('Additional Recruiters')</label>
                <select class="form-control select2" id="recruiters" name="recruiters[]" multiple>
                    @foreach ($recruiters as $recruiter)
                        <option value="{{ $recruiter->id }}" {{ (in_array($recruiter->id, old('employees') ?: []) ?: $account->recruiters->pluck('employeeId')->contains($recruiter->id)) ? 'selected': '' }}>{{ $recruiter->fullName() }}</option>
                    @endforeach
                </select>
                @if ($errors->has('recruiters'))
                    <span class="help-block"><strong>{{ $errors->first('recruiters') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group{{ $errors->has('managerId') ? ' has-error' : '' }}">
                <label for="managerId">@lang('Manager')</label>
                <select class="form-control select2" id="managerId" name="managerId">
                    <option value="" disabled selected></option>
                    @foreach ($managers as $manager)
                        <option value="{{ $manager->id }}" {{ (old('managerId') == $manager->id ?: ($account->manager && $account->manager->employeeId == $manager->id)) ? 'selected': '' }}>{{ $manager->fullName() }}</option>
                    @endforeach
                </select>
                @if ($errors->has('managerId'))
                    <span class="help-block"><strong>{{ $errors->first('managerId') }}</strong></span>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group{{ $errors->has('RSCId') ? ' has-error' : '' }}">
                <label for="RSCId">@lang('Regional Support Center')</label>
                <select class="form-control select2" id="RSCId" name="RSCId">
                    <option value="" disabled selected></option>
                    @foreach ($RSCs as $RSC)
                        <option value="{{ $RSC->id }}" {{ (old('RSCId') == $RSC->id ?: ($RSC->id == $account->RSCId)) ? 'selected': '' }}>{{ $RSC->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('RSCId'))
                    <span class="help-block"><strong>{{ $errors->first('RSCId') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group{{ $errors->has('operatingUnitId') ? ' has-error' : '' }}">
                <label for="operatingUnitId">@lang('Operating Unit')</label>
                <select class="form-control select2" id="operatingUnitId" name="operatingUnitId">
                    <option value="" disabled selected></option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}" {{ (old('operatingUnitId') == $region->id ?: $region->id == $account->operatingUnitId) ? 'selected': '' }}>{{ $region->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('operatingUnitId'))
                    <span class="help-block"><strong>{{ $errors->first('operatingUnitId') }}</strong></span>
                @endif
            </div>
        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-3">
            <div class="form-group{{ $errors->has('practiceId') ? ' has-error' : '' }}">
                <label for="practiceId">@lang('Service Line')</label>
                <select class="form-control select2" id="practiceId" name="practiceId">
                    <option value="" disabled selected></option>
                    @foreach ($practices as $practice)
                        <option value="{{ $practice->id }}" {{ (old('practiceId') == $practice->id ?: $account->practices->contains($practice)) ? 'selected': '' }}>{{ $practice->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('practiceId'))
                    <span class="help-block"><strong>{{ $errors->first('practiceId') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group{{ $errors->has('divisionId') ? ' has-error' : '' }}">
                <label for="divisionId">@lang('Alliance OU Division')</label>
                <select class="form-control select2" id="divisionId" name="divisionId">
                    <option value="" disabled selected></option>
                    @foreach ($divisions as $division)
                        <option value="{{ $division->id }}" {{ (old('divisionId') == $division->id ?: $division->id == $account->divisionId) ? 'selected': '' }}>{{ $division->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('divisionId'))
                    <span class="help-block"><strong>{{ $errors->first('divisionId') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
                <label for="locality">@lang('City')</label>
                <input type="text" class="form-control" id="locality" name="city" value="{{ old('city') ?: $account->city }}" />
                @if ($errors->has('city'))
                    <span class="help-block"><strong>{{ $errors->first('city') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group{{ $errors->has('state') ? ' has-error' : '' }}">
                <label for="administrative_area_level_1">@lang('State')</label>
                <select class="form-control select2" id="administrative_area_level_1" name="state"">
                    @foreach($states as $state)
                        <option value="{{ $state->abbreviation }}" {{ (old('state') == $state->abbreviation ?: $state->abbreviation == $account->state) ? 'selected': '' }}>{{ $state->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('state'))
                    <span class="help-block"><strong>{{ $errors->first('state') }}</strong></span>
                @endif
            </div>
        </div>
    </div>

    <input type="hidden" id="street_number" name="number" value="{{ old('number') ?: $account->number }}" />
    <input type="hidden" id="route" name="street" value="{{ old('street') ?: $account->street }}" />
    <input type="hidden" id="country" name="country" value="{{ old('country') ?: $account->country }}" />
    <input type="hidden" id="postal_code" name="zipCode" value="{{ old('zipCode') ?: $account->zipCode }}" />
    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') ?: $account->latitude }}" />
    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') ?: $account->longitude }}" />

    <hr />

    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('startDate') ? ' has-error' : '' }}">
                <label for="startDate">@lang('Start Date')</label>
                <div class="input-group date datepicker">
                    <input type="text" class="form-control" id="startDate" name="startDate" value="{{ old('startDate') ?: ($account->startDate ? $account->startDate->format('Y-m-d') : '') }}" />
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
                @if ($errors->has('startDate'))
                    <span class="help-block"><strong>{{ $errors->first('startDate') }}</strong></span>
                @endif
            </div>
            @if ($action == 'edit' && $account->isRecentlyCreated())
                <div class="bg-success text-center">
                    <br />
                    @lang('Account created in the last')
                    <strong>6</strong>
                    @lang('months').
                    <br />&nbsp;
                </div>
            @endif
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('endDate') ? ' has-error' : '' }}">
                <label for="endDate">@lang('End Date')</label>
                <div class="input-group date datepicker">
                    <input type="text" class="form-control" id="endDate" name="endDate" value="{{ old('endDate') ?: ($account->endDate ? $account->endDate->format('Y-m-d') : '') }}" />
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
                @if ($errors->has('endDate'))
                    <span class="help-block"><strong>{{ $errors->first('endDate') }}</strong></span>
                @endif
            </div>
            @if ($action == 'edit' && $account->hasEnded())
                <div class="bg-danger text-center">
                    <br />
                    <strong>@lang('Account has ended.')</strong>
                    <br />&nbsp;
                </div>
            @endif
        </div>
    </div>

    {{-- @if ($action == 'edit' && $account->physiciansApps->count())
        <div class="row mb10">
            <div class="col-md-12">
                <a href="javascript:;" data-toggle="modal" data-target="#physicians-apps-history">
                    @lang('Physicians and Apps History')
                </a>
            </div>
        </div>
    @endif --}}
    
    @if ($action == 'edit')
        <div id="physicianAppsChangeConfirmation" style="display: none;" class="row">
            <div class="col-md-6">
                <div class="form-group{{ $errors->has('physicianAppsChangeDate') ? ' has-error' : '' }}">
                    <label for="physicianAppsChangeDate">
                        @lang('Date the changes take effect')
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group date datepicker">
                        <input type="text" class="form-control" id="physicianAppsChangeDate" name="physicianAppsChangeDate" value="{{ old('physicianAppsChangeDate') ?: '' }}" />
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                    @if ($errors->has('physicianAppsChangeDate'))
                        <span class="help-block"><strong>{{ $errors->first('physicianAppsChangeDate') }}</strong></span>
                    @endif
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group{{ $errors->has('physicianAppsChangeReason') ? ' has-error' : '' }}">
                    <label for="physicianAppsChangeReason">
                        @lang('Why they changed?')
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="physicianAppsChangeReason" name="physicianAppsChangeReason" value="{{ old('physicianAppsChangeReason') ?: '' }}" />
                    @if ($errors->has('physicianAppsChangeReason'))
                        <span class="help-block"><strong>{{ $errors->first('physicianAppsChangeReason') }}</strong></span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <hr />

    <div class="row">
        <div class="col-md-12">
            <div class="form-group{{ $errors->has('accountDescription') ? ' has-error' : '' }}">
                <label for="accountDescription">
                    @lang('Account Description')
                </label>
                <textarea class="form-control" id="accountDescription" name="accountDescription">{{ old('accountDescription') ?: $account->accountDescription }}</textarea>
                @if ($errors->has('accountDescription'))
                    <span class="help-block"><strong>{{ $errors->first('accountDescription') }}</strong></span>
                @endif
            </div>
        </div>
    </div>

    <hr />

    <div class="internal-plan-checkboxes">
        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="pressRelease" {{ (old('pressRelease') ?: $account->pressRelease) ? 'checked' : '' }} />
                        @lang('Has a press release gone out announcing newstart, and if so when?')
                    </label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="input-group date datepicker">
                    <input type="text" class="form-control" name="pressReleaseDate" value="{{ old('pressReleaseDate') ?: ($account->pressReleaseDate ? $account->pressReleaseDate->format('Y-m-d') : '') }}" placeholder="When?" />
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="managementChangeMailers" {{ (old('managementChangeMailers') ?: $account->managementChangeMailers) ? 'checked' : '' }} />
                        @lang('Have mailers gone out announcing management change?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="recruitingMailers" {{ (old('recruitingMailers') ?: $account->recruitingMailers) ? 'checked' : '' }} />
                        @lang('Have mailers gone out for recruiting?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="emailBlast" {{ (old('emailBlast') ?: $account->emailBlast) ? 'checked' : '' }} />
                        @lang('Have email blasts gone out?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="purlCampaign" {{ (old('purlCampaign') ?: $account->purlCampaign) ? 'checked' : '' }} />
                        @lang('PURL Campaign')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="marketingSlick" {{ (old('marketingSlick') ?: $account->marketingSlick) ? 'checked' : '' }} />
                        @lang('Account Marketing slick generated')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="collaborationRecruitingTeam" {{ (old('collaborationRecruitingTeam') ?: $account->collaborationRecruitingTeam) ? 'checked' : '' }} />
                        @lang('Do we need to set up a collaboration recruiting team, and if so, who is on the team?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="collaborationRecruitingTeamNames" value="{{ old('collaborationRecruitingTeamNames') ?: $account->collaborationRecruitingTeamNames }}" placeholder="Who is on the team?" />
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="compensationGrid" {{ (old('compensationGrid') ?: $account->compensationGrid) ? 'checked' : '' }} />
                        @lang('What is the compensation grid, including sign on bonuses or retention bonuses?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="compensationGridBonuses" value="{{ old('compensationGridBonuses') ?: $account->compensationGridBonuses }}" placeholder="Compensation grid" />
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="recruitingIncentives" {{ (old('recruitingIncentives') ?: $account->recruitingIncentives) ? 'checked' : '' }} />
                        @lang('What additional recruiting incentives do we have in place?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="recruitingIncentivesDescription" value="{{ old('recruitingIncentivesDescription') ?: $account->recruitingIncentivesDescription }}" placeholder="Additional recruiting incentives" />
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="locumCompaniesNotified" {{ (old('locumCompaniesNotified') ?: $account->locumCompaniesNotified) ? 'checked' : '' }} />
                        @lang('Have you notified the locum companies?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="searchFirmsNotified" {{ (old('searchFirmsNotified') ?: $account->searchFirmsNotified) ? 'checked' : '' }} />
                        @lang('Have you notified the 3rd party search firms?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="departmentsCoordinated" {{ (old('departmentsCoordinated') ?: $account->departmentsCoordinated) ? 'checked' : '' }} />
                        @lang('Have you coordinated with the on site hospital marketing department physicians liaisons and internal recruiter?')
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                
            </div>
        </div>
    </div>
    
    @if ($action == 'edit')
        <div class="row">
            <div class="col-md-12 text-center">
                @permission('admin.accounts.internalPlan')
                    <a href="{{ route('admin.accounts.internalPlan', [$account]) }}" class="btn btn-primary">@lang('Create PDF')</a>
                    <br />
                    @lang('To give internal plan')
                @endpermission
            </div>
        </div>

       {{--  <hr />
        
        <div class="row text-center">
            <div class="col-md-6">
                <a href="javascript:;" class="btn btn-primary">@lang('Print as PDF')</a>
                <br />
                @lang('Marketing Slick')
            </div>
            <div class="col-md-6">
                <a href="javascript:;" class="btn btn-primary">@lang('Email Marketing Slick')</a>
            </div>
        </div> --}}
    @endif

    <hr />
    
    <div class="row">
        <div class="col-md-12 text-right">
            @include('admin.common.submit', [
                'action' => $action,
                'store' => 'admin.accounts.store',
                'update' => 'admin.accounts.update'
            ])
        </div>
    </div>

</form>

<div class="modal fade" id="site-code-history" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ $account->name }} @lang('Site Code History')</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>@lang('Site Code')</th>
                                <th>@lang('Modified At')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($account->siteCodes as $siteCode)
                                <tr>
                                    <td>{{ $siteCode->siteCode }}</td>
                                    <td>{{ $siteCode->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="physicians-apps-history" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ $account->name }} @lang('Physicians and Apps History')</h4>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>@lang('No. of Physicians needed')</th>
                                <th>@lang('No. of APPs needed')</th>
                                <th>@lang('No. of hours for Physicians per month')</th>
                                <th>@lang('No. of hours for APP per month')</th>
                                <th>@lang('Date the changes take effect')</th>
                                <th>@lang('Why they changed?')</th>
                                <th>@lang('Modified At')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($account->physiciansApps as $physicianApp)
                                <tr>
                                    <td>{{ $physicianApp->physiciansNeeded }}</td>
                                    <td>{{ $physicianApp->appsNeeded }}</td>
                                    <td>{{ $physicianApp->physicianHoursPerMonth }}</td>
                                    <td>{{ $physicianApp->appHoursPerMonth }}</td>
                                    <td>{{ $physicianApp->physicianAppsChangeDate }}</td>
                                    <td>{{ $physicianApp->physicianAppsChangeReason }}</td>
                                    <td>{{ $physicianApp->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        #map {
            height: 300px;
        }
    </style>
@endpush


@push('scripts')
    <script>
        @if ($action == 'edit')
            // $(document).ready(function () {
            //     promptForNotesAndDateIfDifferent();
            //     $('#physiciansNeeded, #appsNeeded, #physicianHoursPerMonth, #appHoursPerMonth').on('input', promptForNotesAndDateIfDifferent);
            // });

            // function promptForNotesAndDateIfDifferent() {
            //     var account = BackendVars.account;
            //     if (
            //         $('#physiciansNeeded').val() != account.physiciansNeeded ||
            //         $('#appsNeeded').val() != account.appsNeeded ||
            //         $('#physicianHoursPerMonth').val() != account.physicianHoursPerMonth ||
            //         $('#appHoursPerMonth').val() != account.appHoursPerMonth
            //     ) {
            //         $('#physicianAppsChangeConfirmation').show();
            //         $('#physicianAppsChangeDate, #physicianAppsChangeReason').prop('required', true);
            //     } else {
            //         $('#physicianAppsChangeConfirmation').hide();
            //         $('#physicianAppsChangeDate, #physicianAppsChangeReason').prop('required', false);
            //     }
            // }
        @endif

        function initAutocomplete() {
            var map, marker, mapOptions, markerOptions;
            var lat = Number($('#latitude').val());
            var lng = Number($('#longitude').val());
            // Create the autocomplete object, restricting the search to geographical location types.
            var autocomplete = new google.maps.places.Autocomplete(
                (document.getElementById('googleAddress')), {
                    types: ['geocode'],
                    componentRestrictions: {country: 'us'}
                }
            );

            var componentForm = {
                street_number: 'short_name',
                route: 'long_name',
                locality: 'long_name',
                administrative_area_level_1: 'short_name',
                country: 'long_name',
                postal_code: 'short_name'
            };

            mapOptions = { zoom: 15 };
            mapOptions.center = (lat && lng) ? { lat: lat, lng: lng } : { lat: 42.99092, lng: -71.4682532 };

            // Create a Google Map
            map = new google.maps.Map(document.getElementById('map'), mapOptions);

            markerOptions = { map: map };
            markerOptions.position = (lat && lng) ? { lat: lat, lng: lng } : null;

            // Create a Google Marker
            marker = new google.maps.Marker(markerOptions);

            // When the user selects an address from the dropdown, populate the address fields in the form.
            autocomplete.addListener('place_changed', function () {
                fillInAddress(autocomplete, componentForm, map, marker);
            });
        }

        function fillInAddress(autocomplete, componentForm, map, marker) {
            // Get the place details from the autocomplete object.
            var place = autocomplete.getPlace();
            var location = place.geometry.location;

            for (var component in componentForm) {
                document.getElementById(component).value = '';
                document.getElementById(component).disabled = false;
            }

            // Get each component of the address from the place details and fill the corresponding field on the form.
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    document.getElementById(addressType).value = val;
                }
            }

            // Center map and set marker position.
            map.setCenter(location);
            marker.setPosition(location);
            $('#latitude').val(location.lat());
            $('#longitude').val(location.lng());
        }
        /* END Google Address */

        // Image upload
        function updatePathInput(response) {
            $('#photoPath').val(response.path);
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps.key') }}&libraries=places&callback=initAutocomplete" async defer></script>
@endpush