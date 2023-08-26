// -----------------------------------------------------------------------------
// @see: https://github.com/gruntjs/grunt-contrib-cssmin
// @see: https://github.com/jakubpawlowicz/clean-css#how-to-use-clean-css-programmatically
// npm install grunt-contrib-cssmin --save-dev
// -----------------------------------------------------------------------------

module.exports = function(grunt) {
    return {
        options: {
            keepSpecialComments: 0
        },
        dev: {
            files: [
                {
                    expand: true,
                    cwd: '<%= project.assets %>/css',
                    src: ['**/*.css', '!**/*.min.css'],
                    dest: '<%= project.assets %>/css',
                    filter: 'isFile'
                },
                {
                    expand: true,
                    cwd: '<%= project.media %>/css',
                    src: ['**/*.css', '!**/*.min.css'],
                    dest: '<%= project.media %>/css',
                    filter: 'isFile'
                },
                {
                    expand: true,
                    cwd: '<%= project.media %>/css_secure',
                    src: ['**/*.css', '!**/*.min.css'],
                    dest: '<%= project.media %>/css_secure',
                    filter: 'isFile'
                }
            ]
        }
    };
};
