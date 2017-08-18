// Add CustomEvent to IE9 and IE10 
if (typeof CustomEvent !== 'function') {
    // Code from: https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent
    var CustomEvent = function(event, options) {
        options = options || { bubbles: false, cancelable: false, detail: undefined }
        var e = document.createEvent('CustomEvent')
        e.initCustomEvent(event, options.bubbles, options.cancelable, options.detail)
        return e
    }

    CustomEvent.prototype = window.Event.prototype

    window.CustomEvent = CustomEvent
}

// jQuery
window.$ = window.jQuery = require('jquery');

// Bootstrap
require('bootstrap-sass');

// Admin LTE
require('admin-lte');

// iCheck
require('icheck');

// DataTables
require('datatables');
require('datatables-bootstrap3-plugin');
require('datatables-select');

// Select2
require('select2');

// DateTime Picker
require('bootstrap-datetime-picker');

// DatePicker
require('bootstrap-datepicker');

// Toastr
window.toastr = require('toastr');

// LoDash
window._ = require('lodash');

// Vue
window.Vue = require('vue');

// Axios
require('es6-promise').polyfill();
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;

// FastClick
const FastClick = require('fastclick');
FastClick.attach(document.body);

// Moment
window.moment = require('moment');

// DateRangePicker
const daterangepicker = require('bootstrap-daterangepicker');

window.$.fn.daterangepicker = function(options, callback) {
    this.each(function() {
        var el = $(this);
        if (el.data('daterangepicker'))
            el.data('daterangepicker').remove();
        el.data('daterangepicker', new daterangepicker(el, options, callback));
    });
    return this;
};

// Onload Defaults
require('./app/onload-defaults');
