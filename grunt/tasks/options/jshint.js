// -----------------------------------------------------------------------------
// @see: https://github.com/gruntjs/grunt-contrib-jshint
// @see: http://jshint.com/docs
// @see: http://jshint.com/docs/options
// npm install grunt-contrib-jshint --save-dev
// -----------------------------------------------------------------------------

module.exports = function(grunt) {
    return {
        options: {
            browser: true,
            curly: true,
            eqeqeq: true,
            eqnull: true,
            scripturl: true,
            undef: false,
            sub: true,
            globals: {
                jQuery: true,
                MobileDetect: true,
                Yetii: true
            }
        },
        dev: [
            '<%= project.assets %>/src/js/**/*.js',
            '!<%= project.assets %>/src/js/checkout/**',
            '!<%= project.assets %>/src/js/configurableswatches/**',
            '!<%= project.assets %>/src/js/vendor/**'
        ]
    };
};
