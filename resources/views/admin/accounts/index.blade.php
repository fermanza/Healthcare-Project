@extends('layouts.admin')

@section('content-header', __('Accounts'))

@section('tools')
    <a href="{{ route('admin.accounts.create') }}" class="btn btn-sm btn-success">
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
                    <th class="mw100">@lang('Site Code')</th>
                    <th class="mw150">@lang('Parent Site Code')</th>
                    <th class="mw150">@lang('City')</th>
                    <th class="mw150">@lang('State')</th>
                    <th class="mw150">@lang('Start Date')</th>
                    <th class="mw150">@lang('End Date')</th>
                    <th class="mw200">@lang('Actions')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accounts as $account)
                    <tr class="{{ $account->hasEnded() ? 'danger' : ($account->isRecentlyCreated() ? 'success' : '') }}">
                        <td>{{ $account->name }}</td>
                        <td>{{ $account->siteCode }}</td>
                        <td>{{ $account->parentSiteCode }}</td>
                        <td>{{ $account->city }}</td>
                        <td>{{ $account->state }}</td>
                        <td>{{ $account->startDate ? $account->startDate->format('Y-m-d') : '' }}</td>
                        <td>{{ $account->endDate ? $account->endDate->format('Y-m-d') : '' }}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-xs btn-default btnMergeOrParentSiteCode" 
                                data-toggle="modal" data-target="#mergeOrParentSiteCode" data-submit="@lang('Merge')"
                                data-title="@lang('Merge Site Code')" data-account="{{ $account->name }}"
                                data-action="{{ route('admin.accounts.merge', [$account]) }}"
                                data-site-code="{{ $account->siteCode }}"
                            >
                                @lang('Merge')
                            </button>
                            <button type="button" class="btn btn-xs btn-default btnMergeOrParentSiteCode" 
                                data-toggle="modal" data-target="#mergeOrParentSiteCode" data-submit="@lang('Set Parent')"
                                data-title="@lang('Parent Site Code')" data-account="{{ $account->name }}"
                                data-action="{{ route('admin.accounts.parent', [$account]) }}"
                                data-site-code="{{ $account->siteCode }}"
                            >
                                @lang('Set Parent')
                            </button>
                            <a href="{{ route('admin.accounts.edit', [$account]) }}" class="btn btn-xs btn-primary">
                                <i class="fa fa-pencil"></i>
                            </a>
                            <a 
                                href="javascript:;"
                                class="btn btn-xs btn-danger deletes-record"
                                data-action="{{ route('admin.accounts.destroy', [$account]) }}"
                                data-record="{{ $account->id }}"
                                data-name="{{ $account->name }}"
                            >
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="mergeOrParentSiteCode" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">
                        <span id="mergeOrParentSiteCodeTitle"></span>
                        <small id="mergeOrParentSiteCodeAccount"></small>
                    </h4>
                </div>
                <div class="modal-body">
                    <form id="formMergeOrParentSiteCode" action="" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('PATCH') }}
                        <div class="form-group">
                            <label for="siteCode">@lang('Site Code')</label>
                            <select class="form-control select2" data-parent="#mergeOrParentSiteCode" id="siteCode" name="siteCode" required>
                                <option value="" disabled selected></option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->siteCode }}">{{ $account->siteCode }} - {{ $account->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-xs-6">
                                <button type="submit" class="btn btn-primary">
                                    <span id="mergeOrParentSiteCodeSubmit"></span>
                                </button>
                            </div>
                            <div class="col-xs-6 text-right">
                                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            $('.btnMergeOrParentSiteCode').on('click', function () {
                var action = $(this).data('action');
                var title = $(this).data('title');
                var account = $(this).data('account');
                var submit = $(this).data('submit');
                var siteCode = $(this).data('site-code');
                $('#formMergeOrParentSiteCode').attr('action', action);
                $('#mergeOrParentSiteCodeTitle').text(title);
                $('#mergeOrParentSiteCodeAccount').text(account);
                $('#mergeOrParentSiteCodeSubmit').text(submit);
                $('#siteCode option[value=' + siteCode + ']').attr('disabled', true).siblings('[value!=""][value!="' + siteCode + '"]').removeAttr('disabled');
                $('#siteCode').select2('destroy');
                $('#siteCode').val('');
                $('#siteCode').select2({ dropdownParent: $('#mergeOrParentSiteCode') });
            });
        });
    </script>
@endpush
