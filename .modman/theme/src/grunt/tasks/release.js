module.exports = function(grunt) {
    grunt.registerTask('release', function() {
        grunt.config.set('build', 'release');
        grunt.file.write(grunt.config.get('project.assets') + '/src/scss/_grunt.scss', '$grunt--build: \'' + grunt.config.get('build') + '\';\n');
        grunt.task.run(
            'clean:css',
            'clean:js',
            'clean:cache',
            'clean:skin',
            'sync',
            'copy',
            'css:release',
            'uglify'
        );
    });
    grunt.registerTask('css:release', function() {
        grunt.config.set('build', 'release');
        grunt.task.run(
            'sass:release',
            'autoprefixer',
            'cssmin'
        );
    });
    grunt.registerTask('js:release', function() {
        grunt.config.set('build', 'release');
        grunt.task.run(
            'sync:js',
            'uglify'
        );
    });
};
