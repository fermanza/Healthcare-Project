$(() => {

    // Sidebar status
    $('body').on('expanded.pushMenu', () => {
        $.get('/admin/sidebar-expand');
    });

    $('body').on('collapsed.pushMenu', () => {
        $.get('/admin/sidebar-collapse');
    });


    // iCheck
    $('.icheck').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square-blue',
        increaseArea: '20%'
    });
    

    // DataTables
    $('.datatable').dataTable({
        bStateSave: true
    });


    // Select2
    $('.select2').select2();


    // DateTime Picker
    $('.datetimepicker').datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true,
        todayHighlight: true
    });


    // DatePicker
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    // Image Upload
    require('./image-upload')

    // Delete Records
    $('.deletes-record').on('click', function(e) {
        e.preventDefault();
        const $deleteForm = $('#delete-form');
        const message = $deleteForm.data('message');
        const action = $(this).data('action');
        const record = $(this).data('record');
        const name = $(this).data('name');
        if (confirm(`${message} ${name}?`)) {
            $deleteForm.attr('action', action);
            $deleteForm.submit();
        }
    });

});
