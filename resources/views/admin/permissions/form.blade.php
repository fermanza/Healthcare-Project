@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.permissions.store') : route('admin.permissions.update', [$permission]) }}" method="POST">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-12">

            <div class="form-group">
                <label for="name">@lang('Name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?: $permission->name }}" disabled />
            </div>

            <div class="form-group{{ $errors->has('display_name') ? ' has-error' : '' }}">
                <label for="display_name">@lang('Display Name')</label>
                <input type="text" class="form-control" id="display_name" name="display_name" value="{{ old('display_name') ?: $permission->display_name }}" required />
                @if ($errors->has('display_name'))
                    <span class="help-block"><strong>{{ $errors->first('display_name') }}</strong></span>
                @endif
            </div>

        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-12 text-right">
            @include('admin.common.submit', [
                'action' => $action,
                'store' => 'admin.permissions.store',
                'update' => 'admin.permissions.update'
            ])
        </div>
    </div>
</form>
