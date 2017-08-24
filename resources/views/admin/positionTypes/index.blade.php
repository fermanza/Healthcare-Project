@extends('layouts.admin')

@section('content-header', __('Position Types'))

@section('tools')
    @permission('admin.positionTypes.create')
        <a href="{{ route('admin.positionTypes.create') }}" class="btn btn-sm btn-success">
            <i class="fa fa-plus"></i>
            @lang('New')
        </a>
    @endpermission
@endsection

@section('content')
    <div class="table-responsive mh400">
        <table class="table table-hover table-bordered datatable">
            <thead>
                <tr>
                    <th class="mw200 w100">@lang('Name')</th>
                    <th class="mw50">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($positionTypes as $positionType)
                    <tr>
                        <td>{{ $positionType->name }}</td>
                        <td class="text-center">
                            @permission('admin.positionTypes.edit')
                                <a href="{{ route('admin.positionTypes.edit', [$positionType]) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i>
                                </a>
                            @endpermission
                            
                            @permission('admin.positionTypes.destroy')
                                <a 
                                    href="javascript:;"
                                    class="btn btn-xs btn-danger deletes-record"
                                    data-action="{{ route('admin.positionTypes.destroy', [$positionType]) }}"
                                    data-record="{{ $positionType->id }}"
                                    data-name="{{ $positionType->name }}"
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
