'use strict';

var gulp        = require('gulp'),
    prefixer    = require('gulp-autoprefixer'),
    sass        = require('gulp-sass'),
   	cleanCSS		= require('gulp-clean-css'),
    uglify      = require('gulp-uglify'),
    rename      = require('gulp-rename'),
    normalize   = require('node-normalize-scss'),
    plumber     = require('gulp-plumber'),
    imagemin = require('gulp-imagemin'),
    webp = require('imagemin-webp'),
    extReplace = require('gulp-ext-replace'),
    browserSync = require('browser-sync').create(),
    reload      = browserSync.reload;

require('dotenv').load({path: '.env'});

var input   = './scss/*.scss',
    output  = './',
    scripts = './js/scripts.js',
    htm     = './*.php',
    imgsrc  = 'img/*.{jpg,png}',
    imgout  = 'img/webp';

gulp.task('sass',function(){
  return gulp
    .src(input)
    .pipe(plumber())
    .pipe(sass({
      includePaths: normalize.includePaths
    }).on('error',sass.logError))
    .pipe(prefixer())
		.pipe(cleanCSS())
    .pipe(gulp.dest(output))
    .pipe(browserSync.stream());
});

gulp.task('js',function(){
  return gulp.src(scripts)
    .pipe(rename('scripts.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest('./js/min'))
    .pipe(browserSync.stream());
});

gulp.task('exportWebP', function() {
  return gulp.src(imgsrc)
    .pipe(imagemin([
      webp({
        quality: 80
      })
    ]))
    .pipe(extReplace('.webp'))
    .pipe(gulp.dest(imgout));
});

gulp.task('serve',['sass','js', 'exportWebP'],function(){
  browserSync.init({
    proxy: process.env.DD_URL,
    notify: false
  });
  gulp.watch(imgsrc,['exportWebP']).on('change',reload);
  gulp.watch(input,['sass']).on('change',reload);
  gulp.watch(scripts,['js']).on('change',reload);
});

gulp.task('default',['serve']);
