@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.roles.store') : route('admin.roles.update', [$role]) }}" method="POST">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-12 text-right">
            @include('admin.common.submit', [
                'action' => $action,
                'store' => 'admin.roles.store',
                'update' => 'admin.roles.update'
            ])
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">

            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name">@lang('Name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?: $role->name }}" required />
                @if ($errors->has('name'))
                    <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('display_name') ? ' has-error' : '' }}">
                <label for="display_name">@lang('Display Name')</label>
                <input type="text" class="form-control" id="display_name" name="display_name" value="{{ old('display_name') ?: $role->display_name }}" required />
                @if ($errors->has('display_name'))
                    <span class="help-block"><strong>{{ $errors->first('display_name') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                <label for="description">@lang('Description')</label>
                <input type="text" class="form-control" id="description" name="description" value="{{ old('description') ?: $role->description }}" />
                @if ($errors->has('description'))
                    <span class="help-block"><strong>{{ $errors->first('description') }}</strong></span>
                @endif
            </div>

        </div>
    </div>

    <hr />

    <div class="lead">Permissions</div>

    <div class="flexboxgrid">
        <div class="row">
            @foreach ($lists as $group => $routePermissions)
                <div class="col-xs-12 col-sm-6 col-md-4 box">
                    <h4>{{ $group }}</h4>
                    @foreach ($routePermissions as $permission)
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ (in_array($permission->id, old('permissions') ?: []) ?: $role->permissions->contains($permission)) ? 'checked' : '' }} />
                                {{ $permission->display_name ?: $permission->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @endforeach

            @if ($permissions->count())
                <div class="col-xs-12 col-sm-6 col-md-4 box">
                    <h4>@lang('Others')</h4>
                    @foreach ($permissions as $permission)
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" {{ (in_array($permission->id, old('permissions') ?: []) ?: $role->permissions->contains($permission)) ? 'checked' : '' }} />
                                {{ $permission->display_name ?: $permission->name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-12 text-right">
            @include('admin.common.submit', [
                'action' => $action,
                'store' => 'admin.roles.store',
                'update' => 'admin.roles.update'
            ])
        </div>
    </div>
</form>
