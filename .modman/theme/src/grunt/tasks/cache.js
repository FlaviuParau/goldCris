module.exports = function(grunt) {
    grunt.registerTask('cache', function() {
        grunt.task.run(
            'clean:cache'
        );
    });
};
