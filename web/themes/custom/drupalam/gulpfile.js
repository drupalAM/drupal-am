var gulp= require('gulp');
var browserSync = require('browser-sync').create();
var sass        = require('gulp-sass')(require('node-sass'));

const paths = {
  scss: {
    src: './scss/style.scss',
    dest: './css',
    watch: './scss/**/*.scss',
  }
};

// Compile sass into CSS & auto-inject into browsers
gulp.task('sass', function() {
  return gulp.src(paths.scss.src)
    .pipe(sass())
    .pipe(gulp.dest("css"))
    .pipe(browserSync.stream());
});

// Static Server + watching scss/html files
gulp.task('serve', gulp.series('sass', function() {
  gulp.watch("scss/**/*.scss", gulp.series('sass'));
}));

gulp.task('default', gulp.series('serve'));
