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
window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.head.querySelector('meta[name="csrf-token"]').content;

// FastClick
const FastClick = require('fastclick');
FastClick.attach(document.body);

// Moment
window.moment = require('moment');

// Onload Defaults
require('./app/onload-defaults');
