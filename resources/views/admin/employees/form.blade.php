@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.employees.store') : route('admin.employees.update', [$employee]) }}" method="POST">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-12">

             <div class="form-group{{ $errors->has('person_id') ? ' has-error' : '' }}">
                <label for="person_id">@lang('Person')</label>
                <select class="form-control select2" id="person_id" name="person_id" required>
                    <option value="" disabled selected></option>
                    @foreach ($people as $person)
                        <option value="{{ $person->id }}" {{ (old('person_id') == $person->id ?: $person->id == $employee->person_id) ? 'selected': '' }}>{{ $person->fullName() }}</option>
                    @endforeach
                </select>
                @if ($errors->has('person_id'))
                    <span class="help-block"><strong>{{ $errors->first('person_id') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
                <label for="type">@lang('Type')</label>
                <input type="text" class="form-control" id="type" name="type" value="{{ old('type') ?: $employee->type }}" required />
                @if ($errors->has('type'))
                    <span class="help-block"><strong>{{ $errors->first('type') }}</strong></span>
                @endif
            </div>

            <div class="form-group">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="is_full_time" {{ (old('is_full_time') ?: $employee->is_full_time) ? 'checked' : '' }} />
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
