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
    (() => {
        window.defaultDTOptions = {
            bStateSave: true,
            aLengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'All']
            ]
        };
        
        $('.datatable').each(function () {
            let options = $(this).data('datatable-config') || {};
            options = $.extend({}, defaultDTOptions, options);
            $(this).dataTable(options);
        });
    })();
        

    // Select2
    (() => {
        window.defaultSelect2Options = {
            allowClear: true,
            placeholder: '',
            width: '100%'
        };

        $('.select2').each(function () {
            const parent = $(this).data('parent');
            let options = {};
            if (parent) {
                $(this).css('width', '100%');
                options.dropdownParent = $(parent);
            }
            options = $.extend({}, defaultSelect2Options, options);
            $(this).select2(options);
        });
    })();
        

    // DateTime Picker
    $('.datetimepicker').datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function () {
        const event = new Event('input');
        this.dispatchEvent(event);
    });


    // DatePicker
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function () {
        const event = new Event('input');
        this.dispatchEvent(event);
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


    // Mark required Inputs
    $(':input[required]')
        .closest('.form-group')
        .children('label')
        .append(' <span class="text-danger">*</span>');

});
