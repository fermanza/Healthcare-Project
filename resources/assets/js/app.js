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

// FastClick
const FastClick = require('fastclick');
FastClick.attach(document.body);

// Onload Defaults
require('./app/onload-defaults');
