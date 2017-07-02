@include('common/errors')

<form action="{{ $action == 'create' ? route('admin.files.store') : route('admin.files.update', [$file]) }}" method="POST" enctype="multipart/form-data">
    {{ csrf_field() }}
    {{ $action == 'edit' ? method_field('PATCH') : '' }}

    <div class="row">
        <div class="col-md-12">

            <div class="form-group{{ $errors->has('fileTypeId') ? ' has-error' : '' }}">
                <label for="fileTypeId">@lang('File Type')</label>
                <select class="form-control select2" id="fileTypeId" name="fileTypeId" required>
                    <option value="" disabled selected></option>
                    @foreach ($fileTypes as $fileType)
                        <option value="{{ $fileType->fileTypeId }}" {{ (old('fileTypeId') == $fileType->fileTypeId ?: $fileType->fileTypeId == $file->fileTypeId) ? 'selected': '' }}>{{ $fileType->fileTypeName }}</option>
                    @endforeach
                </select>
                @if ($errors->has('fileTypeId'))
                    <span class="help-block"><strong>{{ $errors->first('fileTypeId') }}</strong></span>
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
                    <div>{{ $file->fileName }}</div>
                </div>
            @endif

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
