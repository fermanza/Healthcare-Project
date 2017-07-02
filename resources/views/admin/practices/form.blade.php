@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.practices.store') : route('admin.practices.update', [$practice]) }}" method="POST">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-12">

            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name">@lang('Name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?: $practice->name }}" required />
                @if ($errors->has('name'))
                    <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('code') ? ' has-error' : '' }}">
                <label for="code">@lang('Code')</label>
                <input type="text" class="form-control" id="code" name="code" value="{{ old('code') ?: $practice->code }}" />
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
                'store' => 'admin.practices.store',
                'update' => 'admin.practices.update'
            ])
        </div>
    </div>
</form>
