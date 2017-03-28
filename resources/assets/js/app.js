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

// FastClick
const FastClick = require('fastclick');
FastClick.attach(document.body);

// Onload Defaults
require('./app/onload-defaults');
