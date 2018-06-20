/**
 * Gulpfile
 * Contains build tasks
 */

const fs = require('fs');
const gulp = require('gulp');
const sass = require('gulp-sass');
const sourcemaps = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
const concat = require('gulp-concat');
const babel = require('gulp-babel');
const minify = require('gulp-minify');
const sequence = require('run-sequence');
const bulkSass = require('gulp-sass-bulk-import');
const trimLines = require('gulp-trimlines');
const liveReload = require('gulp-livereload');
const buildTools = require('./build/build');

// define js paths
const jsPaths = [
  './assets/js/libs/**/*',
  './assets/js/app/**/*',
  './assets/js/core/*',
  './assets/js/common/*',
  './assets/js/admin/*',
  './components/**/script/*',
];

// define sass paths
const sassPaths = [
  './assets/sass/abstract/*',
  './assets/sass/app/*',
  './assets/sass/common/*',
  './assets/sass/libs/**/*',
  './assets/sass/views/*',
  './components/**/sass/*',
];

// watch js/sass files and re-compile on save
gulp.task('app:watch', done => {
  liveReload.listen();
  sequence([
    'sass:watch',
    'js:watch',
  ], done);
});

// watch sass files and re-compile on save
gulp.task('sass:watch', () => {
  gulp.watch(sassPaths, ['sass:preprocess']);
});

// watch js files and re-concatenate on save
gulp.task('js:watch', () => {
  gulp.watch(jsPaths, ['js:concat']);
});

// compile all sass files
gulp.task('sass:preprocess', () => {
  gulp.src('./assets/sass/style.scss')
    .pipe(bulkSass())
    .pipe(sourcemaps.init())
    .pipe(sass({outputStyle: 'expanded'}).on('error', sass.logError))
    .pipe(autoprefixer({
      browsers: ['last 5 versions'],
      cascade: false,
    }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('./'))
    .pipe(liveReload());
});

// concatenate all js files
gulp.task('js:concat', () => {
  gulp.src(jsPaths)
    .pipe(trimLines())
    .pipe(sourcemaps.init())
    .pipe(babel({ presets: ['es2015', 'stage-0'] }))
    .pipe(concat('main.js', {
      newLine: '\n;',
    }))
    .pipe(gulp.dest('./assets/js/'))
    .pipe(liveReload());
});

// create module files
gulp.task('component', () => {
  const appName = process.argv[4];
  const componentName = process.argv[6];

  if (!appName || !componentName) {
    return console.error('Invalid arguments. Make sure to pass an app name and module name.');
  }

  buildTools.createComponent(appName, componentName);
});
