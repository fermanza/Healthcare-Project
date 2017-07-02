@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.employees.store') : route('admin.employees.update', [$employee]) }}" method="POST">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-12">

            <div class="form-group{{ $errors->has('personId') ? ' has-error' : '' }}">
                <label for="personId">@lang('Person')</label>
                <select class="form-control select2" id="personId" name="personId" required {{ $action == 'edit' ? 'disabled' : '' }}>
                    <option value="" disabled selected></option>
                    @foreach ($people as $person)
                        <option value="{{ $person->id }}" {{ (old('personId') == $person->id ?: $person->id == $employee->personId) ? 'selected': '' }}>{{ $person->fullName() }}</option>
                    @endforeach
                </select>
                @if ($errors->has('personId'))
                    <span class="help-block"><strong>{{ $errors->first('personId') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('employeeType') ? ' has-error' : '' }}">
                <label for="employeeType">@lang('Type')</label>
                <input type="text" class="form-control" id="employeeType" name="employeeType" value="{{ old('employeeType') ?: $employee->employeeType }}" required />
                @if ($errors->has('employeeType'))
                    <span class="help-block"><strong>{{ $errors->first('employeeType') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('employementStatusId') ? ' has-error' : '' }}">
                <label for="employementStatusId">@lang('Status')</label>
                <select class="form-control select2" id="employementStatusId" name="employementStatusId" required>
                    <option value="" disabled selected></option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->id }}" {{ (old('employementStatusId') == $status->id ?: $status->id == $employee->employementStatusId) ? 'selected': '' }}>{{ $status->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('employementStatusId'))
                    <span class="help-block"><strong>{{ $errors->first('employementStatusId') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('EDPercent') ? ' has-error' : '' }}">
                <label for="EDPercent">@lang('ED Percent')</label>
                <select class="form-control select2" id="EDPercent" name="EDPercent" required>
                    <option value="" disabled selected></option>
                    <option value="0" {{ (old('EDPercent') == 0 ?: $employee->EDPercent == 0) ? 'selected': '' }}>0.0</option>
                    <option value="0.5" {{ (old('EDPercent') == 0.5 ?: $employee->EDPercent == 0.5) ? 'selected': '' }}>0.5</option>
                    <option value="1" {{ (old('EDPercent') == 1 ?: $employee->EDPercent == 1) ? 'selected': '' }}>1</option>
                </select>
                @if ($errors->has('EDPercent'))
                    <span class="help-block"><strong>{{ $errors->first('EDPercent') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('IPSPercent') ? ' has-error' : '' }}">
                <label for="IPSPercent">@lang('IPS Percent')</label>
                <select class="form-control select2" id="IPSPercent" name="IPSPercent" required>
                    <option value="" disabled selected></option>
                    <option value="0" {{ (old('IPSPercent') == 0 ?: $employee->IPSPercent == 0) ? 'selected': '' }}>0.0</option>
                    <option value="0.5" {{ (old('IPSPercent') == 0.5 ?: $employee->IPSPercent == 0.5) ? 'selected': '' }}>0.5</option>
                    <option value="1" {{ (old('IPSPercent') == 1 ?: $employee->IPSPercent == 1) ? 'selected': '' }}>1</option>
                </select>
                @if ($errors->has('IPSPercent'))
                    <span class="help-block"><strong>{{ $errors->first('IPSPercent') }}</strong></span>
                @endif
            </div>

        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-12 text-right">
            @include('admin.common.submit', [
                'action' => $action,
                'store' => 'admin.employees.store',
                'update' => 'admin.employees.update'
            ])
        </div>
    </div>
</form>
