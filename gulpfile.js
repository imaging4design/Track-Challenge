// Load plugins
var	gulp = require('gulp'),
	uglify = require('gulp-uglify'),
	less = require('gulp-less'),
	autoprefixer = require('gulp-autoprefixer'),
	minifyCSS = require('gulp-minify-css'),
	concatCss = require('gulp-concat-css'),
	rename = require('gulp-rename'),
	notify = require('gulp-notify'),
	concat = require('gulp-concat');

	// jshint = require('gulp-jshint'),
	// uglify = require('gulp-uglify'),
	// imagemin = require('gulp-imagemin'),
	// notify = require('gulp-notify'),
	// cache = require('gulp-cache'),
	// livereload = require('gulp-livereload'),
	// del = require('del');


// Task :: Uglifies all js files
gulp.task('scripts', function (){
	gulp.src('public/js/lib/**/*.js')
	.pipe(uglify())
	.pipe(concat('main.js'))
	.pipe(gulp.dest('public/dist/js'));
});


// Copy fonts to /dist directory
gulp.task('copyfonts', function() {
   gulp.src(['public/fonts/**/*.{eot,svg,ttf,woff,woff2}', 'public/fonts/**/*.css'])
   .pipe(gulp.dest('public/dist/fonts'));
});


// Task :: process LESS files into CSS files
gulp.task('less', function (){
	gulp.src('public/css/styles.less')
	.pipe(less())
	.pipe(autoprefixer('last 2 versions'))
	.pipe(rename('styles.css'))
	.pipe(gulp.dest('public/css'))
	.pipe(notify('Less Compiled, Prefixed and Minified'));
});


// Task :: concat and minify all CSS files
gulp.task('css', ['less'], function () {
    gulp.src('public/css/*.css')
        .pipe(concatCss('styles.css'))
        .pipe(minifyCSS())
        .pipe(rename('style.min.css'))
        .pipe(gulp.dest('public/dist/css'))
        .pipe(notify('CSS concatenated and minified'));
});


// TASK :: concat all js files
gulp.task('concatjs', function(){
	gulp.src([
		'public/lib/aangular.min.js',
		'public/lib/angular-animate.min.js',
		'public/lib/angular-cookies.min.js',
		'public/lib/angular-resource.min.js',
		'public/lib/angular-route.min.js',
		'public/lib/angular-sanitize.min.js',
		'public/lib/angular-dragdrop.min.js',
		'public/lib/bootstrap.min.js',
		'public/lib/jquery-ui-1.10.4.custom.js',
		'public/lib/underscore-min.js'
	])
	.pipe(concat('main.js'))
	.pipe(gulp.dest('public/dist/js'));
});


// Watch Task
// Watches JS
gulp.task('watch', function(){
	gulp.watch('public/js/lib/**/*.js', ['scripts']),
	gulp.watch('public/css/styles.less', ['less']),
	gulp.watch('public/css/*.css', ['css']);
});

gulp.task('default', ['less', 'css', 'copyfonts', 'scripts', 'watch']);




