// -----------------------------------------------------------------------------
// @see: https://github.com/gruntjs/grunt-contrib-copy
// npm install grunt-contrib-copy --save-dev
// -----------------------------------------------------------------------------

module.exports = function(grunt) {
    return {
        dev: {
            expand: true,
            cwd: '<%= project.assets %>/presets/default',
            src: ['**', '!**/README.md'],
            dest: '<%= project.assets %>/blugento',

            // Copy if file does not exist.
            filter: function(filepath) {
                var path = require('path'),
                    dest = path.join(
                        grunt.config('copy.dev.dest'),
                        filepath.replace(/\\/g, '\/').replace(grunt.config('copy.dev.cwd'), '')
                    );

                return !(grunt.file.exists(dest));
            }
        }
    };
};
