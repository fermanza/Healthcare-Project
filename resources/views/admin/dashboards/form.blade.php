@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.dashboards.store') : route('admin.dashboards.update', [$dashboard]) }}" method="POST">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name">@lang('Name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $dashboard->name ? $dashboard->name : ''}}" required />
                @if ($errors->has('name'))
                    <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                <label for="description">@lang('Description')</label>
                <input type="text" class="form-control" id="description" name="description" value="{{ $dashboard->description ? $dashboard->description : ''}}" />
                @if ($errors->has('description'))
                    <span class="help-block"><strong>{{ $errors->first('description') }}</strong></span>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('url') ? ' has-error' : '' }}">
                <label for="url">@lang('URL')</label>
                <input type="text" class="form-control" id="url" name="url" value="{{ $dashboard->url ? $dashboard->url : ''}}" required />
                @if ($errors->has('url'))
                    <span class="help-block"><strong>{{ $errors->first('url') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('users') ? ' has-error' : '' }}">
                <label for="users">@lang('Users')</label>
                <select class="form-control select2" id="users" name="users[]" multiple>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ (in_array($user->id, old('users') ?: []) ?: $dashboard->users->contains($user)) ? 'selected': '' }}>{{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @if ($errors->has('users'))
                    <span class="help-block"><strong>{{ $errors->first('users') }}</strong></span>
                @endif
            </div>
        </div>
    </div>

    <hr />
    
    <div class="row">
        <div class="col-md-12 text-right">
            @include('admin.common.submit', [
                'action' => $action,
                'store' => 'admin.files.store',
                'update' => 'admin.files.update'
            ])
        </div>
    </div>
</form>
