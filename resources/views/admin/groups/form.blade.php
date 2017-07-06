@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.groups.store') : route('admin.groups.update', [$group]) }}" method="POST">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-12">

            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name">@lang('Name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?: $group->name }}" required />
                @if ($errors->has('name'))
                    <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('regionId') ? ' has-error' : '' }}">
                <label for="regionId">@lang('Operating Unit')</label>
                <select class="form-control select2" id="regionId" name="regionId" required>
                    <option value="" disabled selected></option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}" {{ (old('regionId') == $region->id ?: $region->id == $group->regionId) ? 'selected': '' }}>{{ $region->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('regionId'))
                    <span class="help-block"><strong>{{ $errors->first('regionId') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">
                <label for="code">@lang('Code')</label>
                <input type="text" class="form-control" id="code" name="code" value="{{ old('code') ?: $group->code }}" />
                @if ($errors->has('code'))
                    <span class="help-block"><strong>{{ $errors->first('code') }}</strong></span>
                @endif
            </div>

        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-12 text-right">
            @include('admin.common.submit', [
                'action' => $action,
                'store' => 'admin.groups.store',
                'update' => 'admin.groups.update'
            ])
        </div>
    </div>
</form>
