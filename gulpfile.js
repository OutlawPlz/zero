'use strict';

var gulp = require( 'gulp' ),
    sass = require( 'gulp-sass' ),
    autoprefixer = require( 'gulp-autoprefixer' ),
    watch = require( 'gulp-watch' );


// Path
// -----------------------------------------------------------------------------

// Source path.
var src = {

  // Scss path.
  scss: {
    dir: './styles/scss',
    glob: './styles/scss/**/*.scss'
  },

  // Css path.
  css: {
    dir: './styles/css',
    glob: './styles/css/**/*.css'
  }
};

// Destination path.
var dest = {

  css: './styles/css'
};


// Options
// -----------------------------------------------------------------------------

var options = {

  // SASS options.
  sass: {
    errLogToConsole: true,
    outputStyle: 'expanded'
  },

  // Autoprefixer options.
  autoprefixer: {
    browsers: [ 'ie >= 9' ]
  }
};


// Tasks
// -----------------------------------------------------------------------------

// Compile SCSS.
gulp.task( 'sass', function () {
  return gulp.src( src.scss.glob )
    .pipe( sass( options.sass ) ).on( 'error', sass.logError )
    .pipe( autoprefixer( options.autoprefixer ) )
    .pipe( gulp.dest( dest.css ) );
} );

// Call to sass and css.
gulp.task( 'style', [ 'sass' ] );

// Watch for changes.
gulp.task( 'watch', function () {
  return gulp.watch( src.scss.glob, [ 'style' ] )
    .on( 'change', function ( event ) {
      console.log( 'File ' + event.path + ' was ' + event.type + ', running tasks...' );
    } );
} );

// Default task.
gulp.task( 'default', [ 'style' ] );
