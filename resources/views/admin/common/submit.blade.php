@if ($action == 'create')
    @permission($store)
        <button type="submit" class="btn btn-success">
            @lang('Create')
        </button>
    @endpermission
@endif

@if ($action == 'edit')
    @permission($update)
        <button type="submit" class="btn btn-info">
            @lang('Update')
        </button>
    @endpermission
@endif
