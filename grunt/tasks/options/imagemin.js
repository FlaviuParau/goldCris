// -----------------------------------------------------------------------------
// @see: https://github.com/gruntjs/grunt-contrib-imagemin
// npm install grunt-contrib-imagemin --save-dev
// -----------------------------------------------------------------------------

// @see: https://github.com/imagemin/imagemin-mozjpeg
// npm install imagemin-mozjpeg --save-dev
var mozjpeg  = require('imagemin-mozjpeg');

// @see: https://github.com/imagemin/imagemin-pngquant
// npm install imagemin-pngquant --save-dev
var pngquant = require('imagemin-pngquant');

module.exports = function(grunt) {
    return {
        options: {
            use: [
                mozjpeg({
                    progressive: true,
                    quality: 90,
                    tune: 'ms-ssim',
                    dcScanOpt: 2,
                    quantTable: 2
                }),
                pngquant({
                    quality: '85-90',
                    speed: 1
                })
            ]
        },
        dev: {
            files: [
                {
                    expand: true,
                    cwd: '<%= project.assets %>/images',
                    src: ['**/*.{jpg,jpeg,gif,png,svg}'],
                    dest: '<%= project.assets %>/images'
                }
            ]
        }
    };
};
