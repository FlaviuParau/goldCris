module.exports = function(grunt) {
    grunt.registerTask('lint', function() {
        grunt.loadNpmTasks('grunt-sass-lint');
        grunt.task.run(
            'sasslint',
            'jshint'
        );
    });
};
