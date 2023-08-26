// -----------------------------------------------------------------------------
// @see: https://github.com/gruntjs/grunt-contrib-watch
// npm install grunt-contrib-watch --save-dev
// -----------------------------------------------------------------------------

module.exports = function(grunt) {
    return {
        options: {
            dateFormat: function(time) {
                var date = new Date(),
                    year = date.getFullYear(),
                    month = ('0' + (date.getMonth() + 1)).slice(-2),
                    day = ('0' + date.getDate()).slice(-2),
                    hours = ('0' + date.getHours()).slice(-2),
                    minutes = ('0' + date.getMinutes()).slice(-2),
                    seconds = ('0' + date.getSeconds()).slice(-2),
                    successMessage = 'Finished in ' + time.toFixed(3) + ' seconds at ' + year + '-' + month + '-' + day + ' ' + hours + ':' + minutes + ':' + seconds,
                    waitingMessage = 'Waiting for more changes...';

                grunt.log.writeln();
                grunt.log.writeln(successMessage['cyan']);
                grunt.log.writeln();
                grunt.log.writeln(waitingMessage['cyan']);
                grunt.log.writeln();
            }
        },
        scss: {
            options: {
                event: ['added', 'deleted', 'changed'],
                interrupt: true
            },
            files: ['<%= project.assets %>/blugento/scss/**/*.scss', '<%= project.assets %>/src/scss/**/*.scss'],
            tasks: ['css:<%= build %>']
        },
        js: {
            options: {
                event: ['added', 'deleted', 'changed'],
                interrupt: true
            },
            files: ['<%= project.assets %>/src/js/**/*.js'],
            tasks: ['js:<%= build %>']
        },
        images: {
            options: {
                event: ['added', 'deleted', 'changed']
            },
            files: ['<%= project.assets %>/src/images/**'],
            tasks: ['sync:images', 'newer:imagemin:dev']
        },
        images_blugento: {
            options: {
                event: ['added', 'deleted', 'changed']
            },
            files: ['<%= project.assets %>/blugento/images/**'],
            tasks: ['sync:images_blugento', 'newer:imagemin:dev']
        }
    };
};
