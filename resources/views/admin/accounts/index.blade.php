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
        <table id="datatable-accounts" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th class="mw30"></th>
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
                    <tr class="{{ $account->hasEnded() ? 'danger' : ($account->isRecentlyCreated() ? 'success' : '') }}"
                        data-id="{{ $account->id }}" data-name="{{ $account->name }}" data-site-code="{{ $account->siteCode }}"
                    >
                        <td></td>
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
                                data-action="{{ route('admin.accounts.merge') }}" data-id="{{ $account->id }}"
                                data-site-code="{{ $account->siteCode }}"
                            >
                                @lang('Merge')
                            </button>
                            <button type="button" class="btn btn-xs btn-default btnMergeOrParentSiteCode" 
                                data-toggle="modal" data-target="#mergeOrParentSiteCode" data-submit="@lang('Set Parent')"
                                data-title="@lang('Parent Site Code')" data-account="{{ $account->name }}"
                                data-action="{{ route('admin.accounts.parent') }}" data-id="{{ $account->id }}"
                                data-site-code="{{ $account->siteCode }}" data-associated-site-code="{{ $account->parentSiteCode }}"
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
                    </h4>
                </div>
                <div class="modal-body">
                    <form id="formMergeOrParentSiteCode" action="" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('PATCH') }}
                        <div class="well">
                            <strong>@lang('Account(s)')</strong>
                            <div id="mergeOrParentSiteCodeNames"></div>
                        </div>
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
            var accountsDT = $('#datatable-accounts').DataTable($.extend({}, defaultDTOptions, {
                columnDefs: [ {
                    orderable: false,
                    className: 'select-checkbox',
                    targets:   0
                } ],
                select: {
                    style: 'multi',
                    selector: 'td:first-child'
                },
                order: [[ 1, 'asc' ]],
                dom: "<'row'<'col-sm-4 dataTables_buttons mb5'><'col-sm-4 text-center'l><'col-sm-4'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>"
            }));

            var $mergeSelected = $('<button data-toggle="modal" data-target="#mergeOrParentSiteCode" class="btn btn-default">Merge selected</button>').on('click', function () {
                var selected = [];
                accountsDT.rows({ selected: true }).every(function () {
                    var $row = $(this.node());
                    selected.push({
                        id: $row.data('id'),
                        name: $row.data('name'),
                        siteCode: String($row.data('site-code'))
                    });
                });
                if (! selected.length) {
                    alert("@lang('Select at least one Account.')");
                    return false;
                }
                var ids = _.map(selected, 'id');
                var siteCodes = _.map(selected, 'siteCode');
                var names = _.map(selected, 'name');
                showMergeOrParentSiteCodeModal({
                    ids: ids,
                    action: "{{ route('admin.accounts.merge') }}",
                    title: "@lang('Merge Site Code')",
                    submit: "@lang('Merge')",
                    siteCodes: siteCodes,
                    names: names,
                    associatedSiteCode: null
                });
            });
            $('.dataTables_buttons').append($mergeSelected);

            $('#datatable-accounts').on('click', '.btnMergeOrParentSiteCode', function () {
                var $this = $(this);
                showMergeOrParentSiteCodeModal({
                    ids: [$this.data('id')],
                    action: $this.data('action'),
                    title: $this.data('title'),
                    submit: $this.data('submit'),
                    siteCodes: [String($this.data('site-code'))],
                    names: [$this.data('account')],
                    associatedSiteCode: $this.data('associated-site-code')
                });
            });

            function showMergeOrParentSiteCodeModal(options) {
                $('#formMergeOrParentSiteCode').find('input[name="accounts[]"]').remove();
                $('#formMergeOrParentSiteCode').attr('action', options.action);
                $('#mergeOrParentSiteCodeTitle').text(options.title);
                $('#mergeOrParentSiteCodeSubmit').text(options.submit);
                $('#mergeOrParentSiteCodeNames').html(options.names.join('<br />'));
                _.each(options.ids, function (id) {
                    $('#formMergeOrParentSiteCode').append('<input type="hidden" name="accounts[]" value="' + id + '" />');
                });
                $('#siteCode option[value!=""]').each(function () {
                    if (_.includes(options.siteCodes, $(this).val())) {
                        $(this).attr('disabled', true);
                    } else {
                        $(this).attr('disabled', false);
                    }
                });
                $('#siteCode').select2('destroy').val(options.associatedSiteCode || '');
                $('#siteCode').select2({ dropdownParent: $('#mergeOrParentSiteCode') });
            }
        });
    </script>
@endpush
