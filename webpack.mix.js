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
    ]);
   
mix.sass('resources/assets/sass/vendor.scss', 'public/css');

mix.browserSync('cdiweboffice.dev');

if (mix.config.inProduction) {
    mix.version();
}
