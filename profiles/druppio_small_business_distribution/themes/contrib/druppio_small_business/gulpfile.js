'use strict';

var config			= require('./config.js');
var path 			= require('path');
var upath 			= require('upath');
var colors			= require('colors');
var extend			= require('extend');
var browserSync		= require('browser-sync');
var gulp 			= require('gulp');
var sass 			= require('gulp-sass');
var sourcemaps 		= require('gulp-sourcemaps');
var autoprefixer 	= require('gulp-autoprefixer');
var sassGlob 		= require('gulp-sass-glob');
var reload      	= browserSync.reload;

var themePath = '/themes/' + config.theme + '/css/';
var external = config.external;
var sassCfg = config.sass;
var autoprefixerCfg = config.autoprefixer;
var bsCfg = config.browserSync;
console.log(themePath)
// helper function for pretty priting copied files
function copyLog(src, dest) {
	console.log('[' + colors.gray(new Date().toLocaleTimeString()) + ']' + ' Copied: ' + colors.yellow(path.basename(src)) + ' to ' + colors.green(dest));
}

// find destination folder for the requested file
function findDest(vf, p) {
	var found = false;
	var dest = '';

	for (var i = 0, len = p.length; i < len; i++) {
		if (upath.normalize(vf.path).indexOf(p[i]) !== -1) {
			dest = external.dest[i];
			found = true;
			break ;
		}
	}

	return dest;
}

// copy any additional files required by theme
gulp.task('cp', function() {
	if (external === undefined) return ;
	if (external.src === undefined && !(external.src instanceof Array) && 
		external.dest === undefined && !(external.dest instanceof Array)) return ;
	if (external.src.length !== external.dest.length) return ;

	var cleanPaths = [],
		src = external.src;
	
	for (var i = 0, len = src.length; i < len; i++) {
		cleanPaths.push(path.dirname(src[i]))
	}

	return gulp.src(external.src)
		.pipe(gulp.dest(function (vf) {
			var dest = findDest(vf, cleanPaths)
			copyLog(vf.path, dest);
			return dest;
		}));
});

// run browser sync and watch for changes
gulp.task('serve', ['cp', 'sass:dev'], function() {
	var bsConfig = extend(true, {}, bsCfg, { 
		files: [ sassCfg.dest ],
		rewriteRules: [{
			match: new RegExp(themePath, 'g'),
			fn: function (match) {
				var path = sassCfg.dest,
					startsWith = sassCfg.dest.substr(0, 1);

				if (startsWith === '.') {
					path =  sassCfg.dest.slice(1);
				}
				else if (startsWith !== '/') {
					path = '/' + sassCfg.dest;
				}

				return path;
			}
		}] 
	});
	browserSync(bsConfig);

	if (sassCfg.enable)
    	gulp.watch(sassCfg.src, ['sass:dev']);
});

// task for building production version css
// * autoprefixer
gulp.task('sass:prod', function () {
  gulp.src(sassCfg.src)
  	.pipe(sassGlob())
    .pipe(sass(sassCfg.compilerOptions).on('error', sass.logError))
    .pipe(autoprefixer(autoprefixerCfg))
    .pipe(gulp.dest(sassCfg.dest));
});

// task for building development version css
gulp.task('sass:dev', function() {
    return gulp.src(sassCfg.src)
		.pipe(sourcemaps.init())
		.pipe(sassGlob())
        .pipe(sass({sourceComments: true}).on('error', sass.logError))
		.pipe(sourcemaps.write())
        .pipe(gulp.dest(sassCfg.dest))
        .pipe(reload({ stream: true }));
});

// default gulp task
gulp.task('default', ['serve']);
