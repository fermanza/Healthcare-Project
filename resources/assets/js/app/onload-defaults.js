$(() => {

    // $(document).on('focus', '.select2', function() {
    //     if(! $(this).siblings('select.select2').is("[multiple]") && $(this).siblings('select.select2').attr('name') != 'monthEndDate')  {
    //         $(this).siblings('select.select2').select2('open');
    //     }
    // });

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

    $('.icheck-red').iCheck({
        checkboxClass: 'icheckbox_square-red',
        radioClass: 'iradio_square-red',
    }).on('ifClicked', function(evt) {
        $(this).iCheck('uncheck');
        const event = new CustomEvent('change');
        this.dispatchEvent(event);
    });

    $('.icheck-yellow').iCheck({
        checkboxClass: 'icheckbox_square-yellow',
        radioClass: 'iradio_square-yellow',
    }).on('ifClicked', function(evt) {
        $(this).iCheck('uncheck');
        const event = new CustomEvent('change');
        this.dispatchEvent(event);
    });

    $('.icheck-green').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    }).on('ifClicked', function(evt) {
        $(this).iCheck('uncheck');
        const event = new CustomEvent('change');
        this.dispatchEvent(event);
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

        window.defaultDTOptionsWithAll = {
            bStateSave: true,
            aLengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, 'All']
            ],
            iDisplayLength: -1
        };
        
        $('.datatable').each(function () {
            let options = $(this).data('datatable-config') || {};
            options = $.extend({}, defaultDTOptions, options);
            $(this).dataTable(options);
        });

        $('.summary-datatable').each(function () {
            let options = $(this).data('datatable-config') || {};
            options = $.extend({}, defaultDTOptionsWithAll, options);
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
            $(this).select2(options).on('select2:close', function(){
                var selectEl = $(this).parent().parent().next().find('.form-control').first();
                if(selectEl.length) {
                    selectEl.focus();
                }
            });
        }).on('select2:select', function(evt) {
            const event = new CustomEvent('change');
            this.dispatchEvent(event);
        });
    })();
        

    // DateTime Picker
    $('.datetimepicker').datetimepicker({
        format: 'mm/dd/yyyy hh:ii',
        autoclose: true,
        todayHighlight: true
    });

    // Provider Autocomplete
    // $(".providers").each(function () {
    //     $(this).select2({
    //         tags: true,
    //         minimumInputLength: 2
    //     }).on('select2:select', function(e) {
    //         const event = new CustomEvent('change');
    //         this.dispatchEvent(event);
    //     });
    // });

    // DatePicker
    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy',
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function () {
        const event = new CustomEvent('input');
        this.dispatchEvent(event);
    });

    $('.datepicker').on('focusout', function(evt) {
        var date = $(this).val();

        if(date != '') {
            var dateArray = date.split('/');

            if(dateArray[2] && dateArray[2].length == 4) {
                //
            } else {
                alert('Please use the proper format (mm/dd/yyyy) or use the calendar, otherwise data will not be saved/updated.');
                $(this).datepicker().datepicker("setDate", '');
                $(this).val('');
            }
        }
    });

    // DatePicker
    $('.datepicker-future').datepicker({
        format: 'mm/dd/yyyy',
        autoclose: true,
        todayHighlight: true,
        startDate: new Date()
    }).on('changeDate', function () {
        const event = new CustomEvent('input');
        this.dispatchEvent(event);
    });

    $(".yearpicker").datepicker( {
        format: "yyyy",
        viewMode: "years", 
        minViewMode: "years",
        autoclose: true,
    }).on('changeDate', function () {
        const event = new CustomEvent('input');
        this.dispatchEvent(event);
    });

    // DateRangePicker
    $('.rangedatepicker').each(function() {
        var el = $(this);
        $(this).daterangepicker({
            autoUpdateInput: false,
            autoApply: true,
            locale: {
              format: 'MM/DD/YYYY',
            }
        }, function (startDate, endDate) {
            el.val(startDate.format('MM/DD/YYYY') + ' - ' + endDate.format('MM/DD/YYYY'));
        });
    });


    // Image Upload
    require('./image-upload')


    // Make rangedatepicker icon work
    $('div.date > .input-group-addon').on('click', function() {
        $(this).parent().children('input').click();
    });


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
