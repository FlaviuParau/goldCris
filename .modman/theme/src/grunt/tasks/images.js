module.exports = function(grunt) {
    grunt.registerTask('images', function() {
        grunt.task.run(
            'sync:images',
            'sync:images_blugento',
            'imagemin'
        );
    });
};