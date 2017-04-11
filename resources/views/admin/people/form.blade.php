@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.people.store') : route('admin.people.update', [$person]) }}" method="POST">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-12">

            <div class="form-group{{ $errors->has('firstName') ? ' has-error' : '' }}">
                <label for="firstName">@lang('First Name')</label>
                <input type="text" class="form-control" id="firstName" name="firstName" value="{{ old('firstName') ?: $person->firstName }}" required />
                @if ($errors->has('firstName'))
                    <span class="help-block"><strong>{{ $errors->first('firstName') }}</strong></span>
                @endif
            </div>

            <div class="form-group{{ $errors->has('lastName') ? ' has-error' : '' }}">
                <label for="lastName">@lang('Last Name')</label>
                <input type="text" class="form-control" id="lastName" name="lastName" value="{{ old('lastName') ?: $person->lastName }}" required />
                @if ($errors->has('lastName'))
                    <span class="help-block"><strong>{{ $errors->first('lastName') }}</strong></span>
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
