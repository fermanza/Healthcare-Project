@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.divisions.store') : route('admin.divisions.update', [$division]) }}" method="POST">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-12">

            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name">@lang('Name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?: $division->name }}" required />
                @if ($errors->has('name'))
                    <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">
                <label for="code">@lang('Code')</label>
                <input type="text" class="form-control" id="code" name="code" value="{{ old('code') ?: $division->code }}" />
                @if ($errors->has('code'))
                    <span class="help-block"><strong>{{ $errors->first('code') }}</strong></span>
                @endif
            </div>

            <div class="form-group">
                <div class="checkbox icheck">
                    <label>
                        <input type="checkbox" value="1" name="isJV" {{ (old('isJV') ?: $division->isJV) ? 'checked' : '' }} />
                        <strong>@lang('Is JV')</strong>
                    </label>
                </div>
            </div>

             <div class="form-group{{ $errors->has('groupId') ? ' has-error' : '' }}">
                <label for="groupId">@lang('Group')</label>
                <select class="form-control select2" id="groupId" name="groupId" required>
                    <option value="" disabled selected></option>
                    @foreach ($groups as $group)
                        <option value="{{ $group->id }}" {{ (old('groupId') == $group->id ?: $group->id == $division->groupId) ? 'selected': '' }}>{{ $group->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('groupId'))
                    <span class="help-block"><strong>{{ $errors->first('groupId') }}</strong></span>
                @endif
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
