const elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {
    mix.sass('app.scss')
       .copy('node_modules/bootstrap-sass/assets/fonts','public/fonts')
       .copy('bower_components/bootstrap/dist/css','public/css/bootstrap')
       .scripts([
            '../../../bower_components/angular/angular.min.js',
            '../../../bower_components/jquery/dist/jquery.min.js',
            '../../../bower_components/bootstrap/dist/js/bootstrap.min.js',
            'app.js',
        ], 'public/js/app.js');
});
