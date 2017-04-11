@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.employees.store') : route('admin.employees.update', [$employee]) }}" method="POST">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-12">

             <div class="form-group{{ $errors->has('personId') ? ' has-error' : '' }}">
                <label for="personId">@lang('Person')</label>
                <select class="form-control select2" id="personId" name="personId" required>
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

            <div class="form-group">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="isFullTime" {{ (old('isFullTime') ?: $employee->isFullTime) ? 'checked' : '' }} />
                        <strong>@lang('Is Full Time')</strong>
                    </label>
                </div>
            </div>

        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-12 text-right">
            <button type="submit" class="btn {{ $action == 'create' ? 'btn-success' : 'btn-info' }}">
                {{ $action == 'create' ? __('Create') : __('Update') }}
            </button>
        </div>
    </div>
</form>
