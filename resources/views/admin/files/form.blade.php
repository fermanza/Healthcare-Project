@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.files.store') : route('admin.files.update', [$file]) }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-12">

            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                <label for="name">@lang('Name')</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?: $file->name }}" required />
                @if ($errors->has('name'))
                    <span class="help-block"><strong>{{ $errors->first('name') }}</strong></span>
                @endif
            </div>

            @if ($action == 'create')
                <div class="form-group{{ $errors->has('file') ? ' has-error' : '' }}">
                    <label for="file">@lang('File')</label>
                    <input type="file" class="form-control" id="file" name="file" required />
                    @if ($errors->has('file'))
                        <span class="help-block"><strong>{{ $errors->first('file') }}</strong></span>
                    @endif
                </div>
            @else
                <div class="form-group">
                    <label for="file">@lang('Current File')</label>
                    <div>{{ $file->filename }}</div>
                </div>
            @endif

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
