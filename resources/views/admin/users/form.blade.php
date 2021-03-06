@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.users.store') : route('admin.users.update', [$user]) }}" method="POST">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-12">

            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name">@lang('Name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?: $user->name }}" required />
                @if ($errors->has('name'))
                    <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email">@lang('E-mail')</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') ?: $user->email }}" required />
                @if ($errors->has('email'))
                    <span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password">@lang('Password')</label>
                @if ($action == 'create')
                    <input type="password" class="form-control" id="password" name="password" minlength="6" required />
                @else
                    <input type="password" class="form-control" id="password" name="password" minlength="6" placeholder="@lang('Leave blank for no change')" />
                @endif
                @if ($errors->has('password'))
                    <span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('roles') ? ' has-error' : '' }}">
                <label for="roles">@lang('Role')</label>
                <select class="form-control select2" id="roles" name="roles[]" required>
                    <option value="" disabled selected></option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ (in_array($role->id, old('roles') ?: []) ?: $user->roles->contains($role)) ? 'selected': '' }}>{{ $role->display_name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('roles'))
                    <span class="help-block"><strong>{{ $errors->first('roles') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('employeeId') ? ' has-error' : '' }}">
                <label for="employeeId">@lang('Employee')</label>
                <select class="form-control select2" id="employeeId" name="employeeId">
                    <option value="" {{ $user->employeeId ? '' : 'selected' }}>@lang('NONE')</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" {{ (old('employeeId') == $employee->id ?: $employee->id == $user->employeeId) ? 'selected': '' }}>{{ $employee->fullName() }}</option>
                    @endforeach
                </select>
                @if ($errors->has('employeeId'))
                    <span class="help-block"><strong>{{ $errors->first('employeeId') }}</strong></span>
                @endif
            </div>
            
            <div class="form-group{{ $errors->has('RSCIds') ? ' has-error' : '' }}">
                <label for="RSCId">@lang('RSC')</label>
                <select class="form-control select2" id="RSCIds" name="RSCIds[]" multiple>
                    @foreach ($RSCs as $RSC)
                        <option value="{{ $RSC->id }}" {{ (in_array($RSC->id, old('RSCIds') ?: []) ?: $user->RSCs->contains($RSC)) ? 'selected': '' }}>{{ $RSC->name }}</option>
                    @endforeach
                </select>
                @if ($errors->has('RSCId'))
                    <span class="help-block"><strong>{{ $errors->first('RSCId') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('operatingUnitIds') ? ' has-error' : '' }}">
                <label for="operatingUnitId">@lang('Operating Unit')</label>
                <select class="form-control select2" id="operatingUnitIds" name="operatingUnitIds[]" multiple>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}" {{ (in_array($region->id, old('operatingUnits') ?: []) ?: $user->operatingUnits->contains($region)) ? 'selected': '' }}>{{ $region->name }}</option>
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
        <div class="col-md-12 text-right">
            @include('admin.common.submit', [
                'action' => $action,
                'store' => 'admin.users.store',
                'update' => 'admin.users.update'
            ])
        </div>
    </div>
</form>
