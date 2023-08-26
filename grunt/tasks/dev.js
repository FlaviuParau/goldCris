module.exports = function(grunt) {
    grunt.registerTask('dev', function() {
        grunt.config.set('build', 'dev');
        grunt.file.write(grunt.config.get('project.assets') + '/src/scss/_grunt.scss', '$grunt--build: \'' + grunt.config.get('build') + '\';\n');
        grunt.task.run(
            'clean:css',
            'clean:js',
            'clean:cache',
            'sync',
            'copy',
            'css:dev',
            'watch'
        );
    });
    grunt.registerTask('css:dev', function() {
        grunt.config.set('build', 'dev');
        grunt.task.run(
            'sass:dev',
            'autoprefixer'
        );
    });
    grunt.registerTask('js:dev', function() {
        grunt.config.set('build', 'dev');
        grunt.task.run(
            'sync:js'
        );
    });
};
