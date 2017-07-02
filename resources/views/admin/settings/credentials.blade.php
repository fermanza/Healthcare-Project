@extends('layouts.admin')

@section('content-header', __('Change Credentials'))

@section('content')
    @include('common/errors')

    <form action="{{ route('admin.settings.credentials.update') }}" method="POST">
        {{ csrf_field() }}
        {{ method_field('PATCH') }}

        <div class="row">
            <div class="col-md-12">

                <div class="form-group{{ $errors->has('current_password') ? ' has-error' : '' }}">
                    <label for="current_password">@lang('Current Password')</label>
                    <input type="password" class="form-control" id="current_password" name="current_password" value="{{ old('current_password') }}" required />
                    @if ($errors->has('current_password'))
                        <span class="help-block"><strong>{{ $errors->first('current_password') }}</strong></span>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label for="email">@lang('New E-mail')</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Leave blank for no change') }}" />
                    @if ($errors->has('email'))
                        <span class="help-block"><strong>{{ $errors->first('email') }}</strong></span>
                    @endif
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <label for="password">@lang('New Password')</label>
                    <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}" placeholder="{{ __('Leave blank for no change') }}" />
                    @if ($errors->has('password'))
                        <span class="help-block"><strong>{{ $errors->first('password') }}</strong></span>
                    @endif
                </div>

            </div>
        </div>

        <hr />
        
        <div class="row">
            <div class="col-md-12 text-right">
                <button type="submit" class="btn btn-info">
                    {{ __('Update') }}
                </button>
            </div>
        </div>
    </form>
@endsection
