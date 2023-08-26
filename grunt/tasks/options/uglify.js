// -----------------------------------------------------------------------------
// @see: https://github.com/gruntjs/grunt-contrib-uglify
// npm install grunt-contrib-uglify --save-dev
// -----------------------------------------------------------------------------

module.exports = function(grunt) {
    return {
        options: {
            mangle: {
                except: ['jQuery', 'Prototype']
            },
            preserveComments: false,
            compress: {
                drop_console: false, // remove all console.* statements
                global_defs: {
                    DEBUG: true
                }
            },
            banner: '<%= banner %>\n;',
            footer: ';\n'
        },
        dev: {
            files: [
                {
                    expand: true,
                    cwd: '<%= project.assets %>/js',
                    src: ['**/*.js', '!**/*.min.js'],
                    dest: '<%= project.assets %>/js',
                    filter: 'isFile'
                },
                {
                    expand: true,
                    cwd: '<%= project.media %>/js',
                    src: ['**/*.js', '!**/*.min.js'],
                    dest: '<%= project.media %>/js',
                    filter: 'isFile'
                },
                {
                    expand: true,
                    cwd: '<%= project.media %>/js_secure',
                    src: ['**/*.js', '!**/*.min.js'],
                    dest: '<%= project.media %>/js_secure',
                    filter: 'isFile'
                }
            ]
        }
    };
};
