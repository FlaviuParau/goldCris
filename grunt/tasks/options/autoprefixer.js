// -----------------------------------------------------------------------------
// @see: https://github.com/ndmitry/grunt-autoprefixer
// npm install grunt-autoprefixer --save-dev
// -----------------------------------------------------------------------------

module.exports = function(grunt) {
    return {
        options: {
            browsers: [
                'last 2 versions',
                'Explorer >= 8',
                'Safari >= 6',
                '> 1%'
            ]
        },
        dev: {
            expand: true,
            cwd: '<%= project.assets %>/css',
            src: ['**/*.css', '!**/*.min.css'],
            dest: '<%= project.assets %>/css',
            filter: 'isFile'
        }
    };
};
