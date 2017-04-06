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

});
