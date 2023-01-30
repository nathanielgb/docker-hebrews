const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .copy('resources/js/daterangepicker.js', 'public/js')
    .copy('resources/js/moment.js', 'public/js')
    .copy('resources/css/daterangepicker.css', 'public/css')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
        require('autoprefixer'),
    ])
    .sass('resources/scss/style.scss', 'public/css')
    .sass('resources/scss/pos.scss', 'public/css')
    .sass('resources/scss/pos-full.scss', 'public/css')
    .version();
