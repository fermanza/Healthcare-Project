@extends('layouts.admin')

@section('content-header', __('Files'))

@section('tools')
    @permission('admin.files.create')
        <a href="{{ route('admin.files.create') }}" class="btn btn-sm btn-success">
            <i class="fa fa-plus"></i>
            New
        </a>
    @endpermission
@endsection

@section('content')
    <div class="table-responsive">
        <table class="table table-hover table-bordered datatable" data-datatable-config='{"order": [[ 0, "desc" ]]}'>
            <thead>
                <tr>
                    <th class="mw40">@lang('File ID')</th>
                    <th class="mw200 w50">@lang('File Name')</th>
                    <th class="mw200 w50">@lang('File Type')</th>
                    <th class="mw50">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($files as $file)
                    <tr>
                        <td>{{ $file->fileLogId }}</td>
                        <td>{{ $file->fileName }}</td>
                        <td>{{ $file->type->fileTypeName }}</td>
                        <td class="text-center">
                            @permission('admin.files.show')
                                <a href="{{ route('admin.files.show', [$file]) }}" class="btn btn-xs btn-default">
                                    <i class="fa fa-download"></i>
                                </a>
                            @endpermission
                                
                            @permission('admin.files.edit')
                                <a href="{{ route('admin.files.edit', [$file]) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endpermission
                                
                            @permission('admin.files.destroy')
                                <a 
                                    href="javascript:;"
                                    class="btn btn-xs btn-danger deletes-record"
                                    data-action="{{ route('admin.files.destroy', [$file]) }}"
                                    data-record="{{ $file->fileLogId }}"
                                    data-name="{{ $file->name }}"
                                >
                                    <i class="fa fa-trash"></i>
                                </a>
                            @endpermission
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
