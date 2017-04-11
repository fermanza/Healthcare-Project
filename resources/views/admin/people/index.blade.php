@extends('layouts.admin')

@section('content-header', __('People'))

@section('tools')
    <a href="{{ route('admin.people.create') }}" class="btn btn-sm btn-success">
        <i class="fa fa-plus"></i>
        New
    </a>
@endsection

@section('content')
    <div class="table-responsive">
        <table class="table table-hover table-bordered datatable">
            <thead>
                <tr>
                    <th class="mw200 w50">@lang('First Name')</th>
                    <th class="mw200 w50">@lang('Last Name')</th>
                    <th class="mw100">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($people as $person)
                    <tr>
                        <td>{{ $person->firstName }}</td>
                        <td>{{ $person->lastName }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.people.edit', [$person]) }}" class="btn btn-xs btn-primary">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a 
                                href="javascript:;"
                                class="btn btn-xs btn-danger deletes-record"
                                data-action="{{ route('admin.people.destroy', [$person]) }}"
                                data-record="{{ $person->id }}"
                                data-name="{{ $person->fullName() }}"
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
