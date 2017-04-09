@extends('layouts.admin')

@section('content-header', __('Practices'))

@section('tools')
    <a href="{{ route('admin.practices.create') }}" class="btn btn-sm btn-success">
        <i class="fa fa-plus"></i>
        New
    </a>
@endsection

@section('content')
    <div class="table-responsive">
        <table class="table table-hover table-bordered datatable">
            <thead>
                <tr>
                    <th class="mw200 w100">@lang('Name')</th>
                    <th class="mw150">@lang('Code')</th>
                    <th class="mw100">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($practices as $practice)
                    <tr>
                        <td>{{ $practice->name }}</td>
                        <td>{{ $practice->code }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.practices.edit', [$practice]) }}" class="btn btn-xs btn-primary">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a 
                                href="javascript:;"
                                class="btn btn-xs btn-danger deletes-record"
                                data-action="{{ route('admin.practices.destroy', [$practice]) }}"
                                data-record="{{ $practice->id }}"
                                data-name="{{ $practice->name }}"
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
