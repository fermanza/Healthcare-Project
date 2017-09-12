@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.affiliations.store') : route('admin.affiliations.update', [$affiliation]) }}" method="POST">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name">@lang('Name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $affiliation->name ? $affiliation->name : ''}}" required />
                @if ($errors->has('name'))
                    <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('displayName') ? ' has-error' : '' }}">
                <label for="displayName">@lang('displayName')</label>
                <input type="text" class="form-control" id="displayName" name="displayName" value="{{ $affiliation->displayName ? $affiliation->displayName : ''}}" />
                @if ($errors->has('displayName'))
                    <span class="help-block"><strong>{{ $errors->first('displayName') }}</strong></span>
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
