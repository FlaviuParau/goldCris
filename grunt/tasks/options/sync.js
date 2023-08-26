// -----------------------------------------------------------------------------
// @see: https://github.com/tomusdrw/grunt-sync
// npm install grunt-sync --save-dev
// -----------------------------------------------------------------------------

module.exports = function(grunt) {
    return {
        css_blugento: {
            files: [
                {
                    cwd: '<%= project.assets %>/blugento/css',
                    src: ['**', '!**/*.md'],
                    dest: '<%= project.assets %>/css'
                }
            ],
            compareUsing: 'md5',
            updateAndDelete: false
        },
        js: {
            files: [
                {
                    cwd: '<%= project.assets %>/src/js',
                    src: ['**', '!**/*.md'],
                    dest: '<%= project.assets %>/js'
                }
            ],
            compareUsing: 'md5',
            updateAndDelete: false
        },
        images: {
            files: [
                {
                    cwd: '<%= project.assets %>/src/images',
                    src: ['**', '!**/*.md'],
                    dest: '<%= project.assets %>/images'
                }
            ],
            compareUsing: 'md5',
            updateAndDelete: false
        },
        images_blugento: {
            files: [
                {
                    cwd: '<%= project.assets %>/blugento/images',
                    src: ['**', '!**/*.md'],
                    dest: '<%= project.assets %>/images'
                }
            ],
            compareUsing: 'md5',
            updateAndDelete: false
        }
    };
};
