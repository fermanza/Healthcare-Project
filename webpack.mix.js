const { mix } = require('laravel-mix');

mix.autoload({
    jquery: ['$', 'window.jQuery', 'jQuery']
});

mix.js('resources/assets/js/app.js', 'public/js')
   .extract([
        'jquery',
        'bootstrap-sass',
        'admin-lte',
        'icheck',
        'fastclick',
        'datatables',
        'datatables-bootstrap3-plugin',
        'select2',
        'bootstrap-datetime-picker',
        'bootstrap-datepicker',
        'toastr',
    ]);
   
mix.sass('resources/assets/sass/vendor.scss', 'public/css');
mix.sass('resources/assets/sass/app.scss', 'public/css');

mix.browserSync('emcare.dev');

if (mix.config.inProduction) {
    mix.version();
}
