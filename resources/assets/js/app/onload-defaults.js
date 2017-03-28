$(() => {

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

});
