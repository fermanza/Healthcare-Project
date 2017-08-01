@include('common/errors')

<form style="font-size: 1.1em;" action="{{ $action == 'create' ? route('admin.contractLogs.store') : route('admin.contractLogs.update', [$contractLog]) }}" method="POST">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="flexboxgrid">
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group">
                    <label for="providerLastName">@lang('Provider Last Name')</label>
                    <input type="text" class="form-control" id="providerLastName" name="providerLastName" value="{{ old('providerLastName') ?: $contractLog->providerLastName }}" required />
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group">
                    <label for="providerMiddleInitial">@lang('Provider Middle Initial')</label>
                    <input type="text" class="form-control" id="providerMiddleInitial" name="providerMiddleInitial" value="{{ old('providerMiddleInitial') ?: $contractLog->providerMiddleInitial }}" />
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group">
                    <label for="providerFirstName">@lang('Provider First Name')</label>
                    <input type="text" class="form-control" id="providerFirstName" name="providerFirstName" value="{{ old('providerFirstName') ?: $contractLog->providerFirstName }}" required />
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('providerDesignationId') ? ' has-error' : '' }}">
                    <label for="providerDesignationId">@lang('Provider Designation')</label>
                    <select class="form-control select2" id="providerDesignationId" name="providerDesignationId" required>
                        <option value="" disabled selected></option>
                        @foreach ($designations as $designation)
                            <option value="{{ $designation->id }}" {{ (old('providerDesignationId') == $designation->id ?: $designation->id == $contractLog->providerDesignationId) ? 'selected': '' }}>{{ $designation->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('providerDesignationId'))
                        <span class="help-block"><strong>{{ $errors->first('providerDesignationId') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('statusId') ? ' has-error' : '' }}">
                    <label for="statusId">@lang('Status')</label>
                    <select class="form-control select2" id="statusId" name="statusId" required>
                        <option value="" disabled selected></option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->id }}" {{ (old('statusId') == $status->id ?: $status->id == $contractLog->statusId) ? 'selected': '' }}>{{ $status->contractStatus }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('statusId'))
                        <span class="help-block"><strong>{{ $errors->first('statusId') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('accountId') ? ' has-error' : '' }}">
                    <label for="accountId">@lang('Site Code')</label>
                    <select class="form-control select2" id="accountId" name="accountId" required>
                        <option value="" disabled selected></option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" {{ (old('accountId') == $account->id ?: $account->id == $contractLog->accountId) ? 'selected': '' }}>{{ $account->siteCode }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('accountId'))
                        <span class="help-block"><strong>{{ $errors->first('accountId') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group">
                    <label for="division">@lang('Alliance OU Division')</label>
                    <input type="text" class="form-control" id="division" name="division" value="{{ ($contractLog->account && $contractLog->account->division) ? $contractLog->account->division->name : '' }}" readonly />
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group">
                    <label for="hospitalName">@lang('Hospital Name')</label>
                    <input type="text" class="form-control" id="hospitalName" name="hospitalName" value="{{ $contractLog->account ? $contractLog->account->name : '' }}" readonly />
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group">
                    <label for="group">@lang('Group')</label>
                    <input type="text" class="form-control" id="group" name="group" value="{{ ($contractLog->account && $contractLog->account->division && $contractLog->account->division->group) ? $contractLog->account->division->group->name : '' }}" readonly />
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group">
                    <label for="practice">@lang('Service Line')</label>
                    <input type="text" class="form-control" id="practice" name="practice" value="{{ ($contractLog->account && $contractLog->account->practices->count()) ? $contractLog->account->practices->first()->name : '' }}" readonly />
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('contractOutDate') ? ' has-error' : '' }}">
                    <label for="contractOutDate">@lang('Contract Out')</label>
                    <div class="input-group date datepicker">
                        <input type="text" class="form-control" id="contractOutDate" name="contractOutDate" value="{{ old('contractOutDate') ?: ($contractLog->contractOutDate ? $contractLog->contractOutDate->format('Y-m-d') : '') }}" required />
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                    @if ($errors->has('contractOutDate'))
                        <span class="help-block"><strong>{{ $errors->first('contractOutDate') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('contractInDate') ? ' has-error' : '' }}">
                    <label for="contractInDate">@lang('Contract In')</label>
                    <div class="input-group date datepicker">
                        <input type="text" class="form-control" id="contractInDate" name="contractInDate" value="{{ old('contractInDate') ?: ($contractLog->contractInDate ? $contractLog->contractInDate->format('Y-m-d') : '') }}" />
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                    @if ($errors->has('contractInDate'))
                        <span class="help-block"><strong>{{ $errors->first('contractInDate') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('counterSigDate') ? ' has-error' : '' }}">
                    <label for="counterSigDate">@lang('Counter Sig')</label>
                    <div class="input-group date datepicker">
                        <input type="text" class="form-control" id="counterSigDate" name="counterSigDate" value="{{ old('counterSigDate') ?: ($contractLog->counterSigDate ? $contractLog->counterSigDate->format('Y-m-d') : '') }}" />
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                    @if ($errors->has('counterSigDate'))
                        <span class="help-block"><strong>{{ $errors->first('counterSigDate') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('sentToQADate') ? ' has-error' : '' }}">
                    <label for="sentToQADate">@lang('Sent to Q/A')</label>
                    <div class="input-group date datepicker">
                        <input type="text" class="form-control" id="sentToQADate" name="sentToQADate" value="{{ old('sentToQADate') ?: ($contractLog->sentToQADate ? $contractLog->sentToQADate->format('Y-m-d') : '') }}" />
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                    @if ($errors->has('sentToQADate'))
                        <span class="help-block"><strong>{{ $errors->first('sentToQADate') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('sentToPayrollDate') ? ' has-error' : '' }}">
                    <label for="sentToPayrollDate">@lang('Sent to Payroll')</label>
                    <div class="input-group date datepicker">
                        <input type="text" class="form-control" id="sentToPayrollDate" name="sentToPayrollDate" value="{{ old('sentToPayrollDate') ?: ($contractLog->sentToPayrollDate ? $contractLog->sentToPayrollDate->format('Y-m-d') : '') }}" />
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                    @if ($errors->has('sentToPayrollDate'))
                        <span class="help-block"><strong>{{ $errors->first('sentToPayrollDate') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('projectedStartDate') ? ' has-error' : '' }}">
                    <label for="projectedStartDate">@lang('Projected Start Date')</label>
                    <div class="input-group date datepicker">
                        <input type="text" class="form-control" id="projectedStartDate" name="projectedStartDate" value="{{ old('projectedStartDate') ?: ($contractLog->projectedStartDate ? $contractLog->projectedStartDate->format('Y-m-d') : '') }}" required />
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                    @if ($errors->has('projectedStartDate'))
                        <span class="help-block"><strong>{{ $errors->first('projectedStartDate') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('actualStartDate') ? ' has-error' : '' }}">
                    <label for="actualStartDate">@lang('Actual Start Date')</label>
                    <div class="input-group date datepicker">
                        <input type="text" class="form-control" id="actualStartDate" name="actualStartDate" value="{{ old('actualStartDate') ?: ($contractLog->actualStartDate ? $contractLog->actualStartDate->format('Y-m-d') : '') }}" />
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                    @if ($errors->has('actualStartDate'))
                        <span class="help-block"><strong>{{ $errors->first('actualStartDate') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('recruiterId') ? ' has-error' : '' }}">
                    <label for="recruiterId">@lang('Recruiter')</label>
                    <select class="form-control select2" id="recruiterId" name="recruiterId" required>
                        <option value="" disabled selected></option>
                        @foreach ($recruiters as $recruiter)
                            <option value="{{ $recruiter->id }}" {{ (old('recruiterId') == $recruiter->id ?: $recruiter->id == $contractLog->recruiterId) ? 'selected': '' }}>{{ $recruiter->fullName() }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('recruiterId'))
                        <span class="help-block"><strong>{{ $errors->first('recruiterId') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('managerId') ? ' has-error' : '' }}">
                    <label for="managerId">@lang('Manager')</label>
                    <select class="form-control select2" id="managerId" name="managerId" required>
                        <option value="" disabled selected></option>
                        @foreach ($managers as $manager)
                            <option value="{{ $manager->id }}" {{ (old('managerId') == $manager->id ?: $manager->id == $contractLog->managerId) ? 'selected': '' }}>{{ $manager->fullName() }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('managerId'))
                        <span class="help-block"><strong>{{ $errors->first('managerId') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('contractCoordinatorId') ? ' has-error' : '' }}">
                    <label for="contractCoordinatorId">@lang('Contract Coordinator')</label>
                    <select class="form-control select2" id="contractCoordinatorId" name="contractCoordinatorId" required>
                        <option value="" disabled selected></option>
                        @foreach ($coordinators as $coordinator)
                            <option value="{{ $coordinator->id }}" {{ (old('contractCoordinatorId') == $coordinator->id ?: $coordinator->id == $contractLog->contractCoordinatorId) ? 'selected': '' }}>{{ $coordinator->fullName() }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('contractCoordinatorId'))
                        <span class="help-block"><strong>{{ $errors->first('contractCoordinatorId') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('contractTypeId') ? ' has-error' : '' }}">
                    <label for="contractTypeId">@lang('Contract Type')</label>
                    <select class="form-control select2" id="contractTypeId" name="contractTypeId" required>
                        <option value="" disabled selected></option>
                        @foreach ($types as $type)
                            <option value="{{ $type->id }}" {{ (old('contractTypeId') == $type->id ?: $type->id == $contractLog->contractTypeId) ? 'selected': '' }}>{{ $type->contractType }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('contractTypeId'))
                        <span class="help-block"><strong>{{ $errors->first('contractTypeId') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('contractNoteId') ? ' has-error' : '' }}">
                    <label for="contractNoteId">@lang('Notes')</label>
                    <select class="form-control select2" id="contractNoteId" name="contractNoteId">
                        <option value="" disabled selected></option>
                        @foreach ($notes as $note)
                            <option value="{{ $note->id }}" {{ (old('contractNoteId') == $note->id ?: $note->id == $contractLog->contractNoteId) ? 'selected': '' }}>{{ $note->contractNote }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('contractNoteId'))
                        <span class="help-block"><strong>{{ $errors->first('contractNoteId') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('specialtyId') ? ' has-error' : '' }}">
                    <label for="specialtyId">@lang('Specialty')</label>
                    <select class="form-control select2" id="specialtyId" name="specialtyId" required>
                        <option value="" disabled selected></option>
                        @foreach ($specialties as $specialty)
                            <option value="{{ $specialty->id }}" {{ (old('specialtyId') == $specialty->id ?: $specialty->id == $contractLog->specialtyId) ? 'selected': '' }}>{{ $specialty->specialty }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('specialtyId'))
                        <span class="help-block"><strong>{{ $errors->first('specialtyId') }}</strong></span>
                    @endif
                </div>
            </div>
            
            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('positionId') ? ' has-error' : '' }}">
                    <label for="positionId">@lang('Phys/MLP')</label>
                    <select class="form-control select2" id="positionId" name="positionId" required>
                        <option value="" disabled selected></option>
                        @foreach ($positions as $position)
                            <option value="{{ $position->id }}" {{ (old('positionId') == $position->id ?: $position->id == $contractLog->positionId) ? 'selected': '' }}>{{ $position->position }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('positionId'))
                        <span class="help-block"><strong>{{ $errors->first('positionId') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('numOfHours') ? ' has-error' : '' }}">
                    <label for="numOfHours">@lang('No. of Hours')</label>
                    <input type="number" class="form-control" id="numOfHours" name="numOfHours" min="0" value="{{ old('numOfHours') ?: $contractLog->numOfHours }}" required />
                    @if ($errors->has('numOfHours'))
                        <span class="help-block"><strong>{{ $errors->first('numOfHours') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('value') ? ' has-error' : '' }}">
                    <label for="value">@lang('Value')</label>
                    <input type="number" class="form-control" id="value" name="value" min="0" max="1" step="0.5" value="{{ old('value') ?: $contractLog->value }}" required />
                    @if ($errors->has('value'))
                        <span class="help-block"><strong>{{ $errors->first('value') }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="form-group{{ $errors->has('comments') ? ' has-error' : '' }}">
                    <label for="comments">@lang('Comments')</label>
                    <input type="text" class="form-control" id="comments" name="comments" value="{{ old('comments') ?: $contractLog->comments }}" />
                    @if ($errors->has('comments'))
                        <span class="help-block"><strong>{{ $errors->first('comments') }}</strong></span>
                    @endif
                </div>
            </div>
        </div>

        <hr />
        
        <div class="row">
            <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3">
                <div class="form-group{{ $errors->has('accounts') ? ' has-error' : '' }}">
                    <label for="accounts[]">@lang('Additional Accounts')</label>
                    <select class="form-control select2" id="accounts" name="accounts[]" multiple>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}" {{ (in_array($account->id, old('accounts') ?: []) ?: $additionalAccounts->contains($account)) ? 'selected': '' }}>{{ $account->siteCode }} - {{ $account->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('accounts'))
                        <span class="help-block"><strong>{{ $errors->first('accounts') }}</strong></span>
                    @endif
                </div>
            </div>
        </div>

        <hr />
        
        <div class="row">
            <div class="col-md-12 text-right">
                @include('admin.common.submit', [
                    'action' => $action,
                    'store' => 'admin.contractLogs.store',
                    'update' => 'admin.contractLogs.update'
                ])
            </div>
        </div>
    </div>
</form>

@push('scripts')
    <script>
        $(document).ready(function () {
            $('#accountId').each(setAccountRelatedInput);
            $('#accountId').on('change', setAccountRelatedInput);

            $('#statusId').on('change', function () {
                var statusId = Number($(this).val());
                var status = _.find(BackendVars.statuses, { id: statusId });

                if (status) {
                    $('#value').val(status.value);
                } else {
                    $('#value').val('');
                }
            });

            function setAccountRelatedInput() {
                var accountId = $(this).val();
                if (accountId) {
                    $.get('/admin/accounts/' + accountId, function(response) {
                        var account = response;
                        $('#division').val(account.division && account.division.name || 'NO DIVISION ASSOCIATED');
                        $('#hospitalName').val(account.name);
                        $('#group').val(account.division && account.division.group && account.division.group.name || 'NO GROUP ASSOCIATED');
                        $('#practice').val(account.practices.length && account.practices[0].name || 'NO PRACTICE ASSOCIATED');
                    });
                } else {
                    $('#division').val('');
                    $('#hospitalName').val('');
                    $('#group').val('');
                    $('#practice').val('');
                }
            }
        });
    </script>
@endpush
