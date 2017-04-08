@extends('layouts.admin')

@section('content-header', __('Files'))

@section('tools')
    <a href="{{ route('admin.files.create') }}" class="btn btn-sm btn-success">
        <i class="fa fa-plus"></i>
        New
    </a>
@endsection

@section('content')
    <div class="table-responsive">
        <table class="table table-hover table-bordered datatable">
            <thead>
                <tr>
                    <th>@lang('Name')</th>
                    <th>@lang('File Name')</th>
                    <th>@lang('Created At')</th>
                    <th>@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($files as $file)
                    <tr>
                        <td>{{ $file->name }}</td>
                        <td>{{ $file->filename }}</td>
                        <td>{{ $file->created_at }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.files.edit', [$file]) }}" class="btn btn-xs btn-primary">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a 
                                href="javascript:;"
                                class="btn btn-xs btn-danger deletes-record"
                                data-action="{{ route('admin.files.destroy', [$file]) }}"
                                data-record="{{ $file->id }}"
                                data-name="{{ $file->name }}"
                            >
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
