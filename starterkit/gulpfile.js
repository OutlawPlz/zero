'use strict';

var fs = require( 'fs' ),
    path = require( 'path' ),
    gulp = require( 'gulp' ),
    sass = require( 'gulp-sass' ),
    concat = require( 'gulp-concat' ),
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

  var folders = fs.readdirSync( src.scss.dir ).filter( function ( item ) {
    return fs.statSync( path.join( src.scss.dir, item ) ).isDirectory();
  } );

  return folders.map( function ( folder ) {
    return gulp.src( path.join( src.scss.dir, folder, '/*.scss' ) )
      .pipe( sass( options.sass ) ).on( 'error', sass.logError )
      .pipe( concat( folder + '.css' ) )
      .pipe( autoprefixer( options.autoprefixer ) )
      .pipe( gulp.dest( dest.css ) )
  } )
} );

// Concat CSS.
gulp.task( 'css', [ 'sass' ], function () {

  var folders = fs.readdirSync( src.css.dir ).filter( function ( item ) {
    return fs.statSync( path.join( src.css.dir, item ) ).isDirectory();
  } );

  return folders.map( function ( folder ) {
    return gulp.src( path.join( src.css.dir, folder, '/*.css' ) )
      .pipe( concat( folder + '.css' ) )
      .pipe( gulp.dest( dest.css ) )
  } );
} );

// Call to sass and css.
gulp.task( 'style', [ 'css' ] );

// Watch for changes.
gulp.task( 'watch', function () {
  return gulp.watch( src.scss.glob, [ 'style' ] )
    .on( 'change', function ( event ) {
      console.log( 'File ' + event.path + ' was ' + event.type + ', running tasks...' );
    } );
} );

// Default task.
gulp.task( 'default', [ 'style' ] );
