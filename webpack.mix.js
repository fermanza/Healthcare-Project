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
    ]);
   
mix.sass('resources/assets/sass/vendor.scss', 'public/css');

mix.browserSync('account-admin.dev');

if (mix.config.inProduction) {
    mix.version();
}
