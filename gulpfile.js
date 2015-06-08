var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Less
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.less('app.less');

	// Compile all css
	mix.styles([
		'vendor/bootstrap-datepicker/datepicker.min.css',
		'vendor/datatables/media/css/jquery.dataTables.min.css',
		'vendor/select2/dist/css/select2.min.css',
		'css/custom.css'
	], 'public/css/app2.css', 'resources/assets');
	
	// Compile css and less
	mix.styles([
		'app.css',
		'app2.css'
	], 'public/css/app.css', 'public/css');
	
	// Compile all scripts
	mix.scripts([
		'vendor/jquery/jquery.min.js',
		'vendor/bootstrap/bootstrap.min.js',
		'vendor/bootstrap-datepicker/bootstrap-datepicker.min.js',
		'vendor/datatables/media/js/jquery.dataTables.min.js',
		'vendor/handlebars/handlebars.min.js',		
		'vendor/select2/dist/js/select2.min.js',
		'js/date_add_date.js',
		'js/datepicker_fix.js',
		'js/handlebars_helpers.js',
		'js/js_php_date.js',				
	], 'public/js/app.js', 'resources/assets');

	// Versioning
	mix.version([
		'public/js/app.js',
		'public/css/app.css'
	]);

    // Copy images and fonts
    mix.copy('public/fonts', 'public/build/fonts');
    mix.copy('resources/assets/vendor/datatables/media/images', 'public/build/images');
});
