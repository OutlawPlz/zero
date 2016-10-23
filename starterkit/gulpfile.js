var gulp = require( 'gulp' ),
    sass = require( 'gulp-sass' ),
    autoprefixer = require( 'gulp-autoprefixer' ),
    watch = require( 'gulp-watch' );

var breakpointsass = './node_modules/breakpoint-sass/stylesheets/',
    singularitygs = './node_modules/singularitygs/stylesheets/';


// Path
// -----------------------------------------------------------------------------

var path = {

  // Styles path.
  styles: {
    sass: [ './styles/scss/**/*.scss' ],
    css: [ './styles/css/**/*.css' ],
    output: './styles/css'
  }
};


// Options
// -----------------------------------------------------------------------------

var options = {

  // SASS options.
  sass: {
    errLogToConsole: true,
    outputStyle: 'expanded',
    includePaths: [
      breakpointsass,
      singularitygs
    ]
  },

  // Autoprefixer options.
  autoprefixer: {
    browsers: [ 'ie >= 9' ]
  }
};


// Tasks
// -----------------------------------------------------------------------------

// Compile SCSS.
gulp.task( 'style', function () {
  return gulp.src( path.styles.sass )
    .pipe( sass( options.sass ) ).on( 'error', sass.logError() )
    .pipe( autoprefixer( options.autoprefixer ) )
    .pipe( gulp.dest( path.styles.output ) )
} );

// Watch for changes.
gulp.task( 'watch', function () {
  return gulp.watch( path.styles.sass [ 'style' ] )
    .on( 'change', function ( event ) {
      console.log( 'File ' + event.path + ' was ' + event.type + ', running tasks...' );
    } )
} );

// Default task.
gulp.task( 'default', ['style'] );
