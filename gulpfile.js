const gulp = require('gulp');
const concat = require('gulp-concat');
const revAll = require("gulp-rev-all");
const gzip = require('gulp-gzip');


function cssConcat () {
    return gulp.src([
        'node_modules/bootstrap/dist/css/bootstrap-grid.css',
        'public/css/app.css'
    ])
        .pipe(concat('app.css'))
        .pipe(revAll.revision({fileNameManifest: 'mix-manifest.json'}))
        .pipe(gulp.dest("public/assets/css"))
        .pipe(gzip())
        .pipe(gulp.dest("public/assets/css"))
        .pipe(revAll.manifestFile())
        .pipe(gulp.dest("public/assets/css"));
};

exports.cssconcat = cssConcat;
