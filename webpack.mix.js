let mix = require('laravel-mix');
var path = require('path');
var webpack = require('webpack');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.webpackConfig({
    resolve: {
        alias: {
            '@': path.resolve('resources/assets/js/app/')
        },
    },
    plugins: [
         new webpack.ProvidePlugin({
             $: 'jquery',
             'window.jQuery': 'jquery',
             jquery: 'jquery',
             jQuery: 'jquery',
             moment: 'moment',
             metismenu: 'metismenu',
         }),
     ]
});

mix.styles([
    'resources/assets/css/bootstrap.min.css',
    'resources/assets/font-awesome/css/font-awesome.css',
    'resources/assets/css/plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox.css',
    'resources/assets/css/animate.css',
    'resources/assets/css/inspinia.css',
    'resources/assets/css/customStyleBlade.css',
],'public/css/public.css');

mix.js('resources/assets/js/app.js', 'public/js/app.js');

mix.copyDirectory([
    'resources/assets/fonts/',
    'resources/assets/font-awesome/fonts/'
],'public/fonts/', false);

mix.copyDirectory('resources/assets/img', 'public/img');
mix.copyDirectory('resources/assets/css/patterns', 'public/css/patterns');
mix.copyDirectory('resources/assets/videos', 'public/videos');

mix.version();
