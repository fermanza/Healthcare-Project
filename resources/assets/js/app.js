// jQuery
window.$ = window.jQuery = require('jquery');

// Bootstrap
require('bootstrap-sass');

// Admin LTE
require('admin-lte');

// iCheck
require('icheck');

// iCheck
const FastClick = require('fastclick');
FastClick.attach(document.body);

// Onload Defaults
require('./app/onload-defaults');
