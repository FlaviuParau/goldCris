// -----------------------------------------------------------------------------
// @see: https://github.com/sindresorhus/grunt-sass
// npm install grunt-sass --save-dev
// -----------------------------------------------------------------------------

module.exports = function(grunt) {
    return {
        options: {
            outFile: '<%= project.assets %>',
            outputStyle: 'expanded'
        },
        dev: {
            options: {
                debugInfo: true,
                sourceComments: true,
                sourceMap: true
            },
            files: [
                {
                    expand: true,
                    cwd: '<%= project.assets %>/src/scss',
                    src: ['**/*.scss'],
                    dest: '<%= project.assets %>/css',
                    ext: '.css',
                    extDot: 'last',
                    filter: 'isFile'
                }
            ]
        },
        release: {
            files: [
                {
                    expand: true,
                    cwd: '<%= project.assets %>/src/scss',
                    src: ['**/*.scss'],
                    dest: '<%= project.assets %>/css',
                    ext: '.css',
                    extDot: 'last',
                    filter: 'isFile'
                }
            ]
        }
    };
};
