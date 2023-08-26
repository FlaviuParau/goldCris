module.exports = function(grunt) {

    // -------------------------------------------------------------------------
    // @see: https://github.com/sindresorhus/time-grunt
    // npm install time-grunt --save-dev
    // -------------------------------------------------------------------------
    if (process.env.NODE_ENV !== 'production') {
        require('time-grunt')(grunt);
    }

    // -------------------------------------------------------------------------
    // @see: https://github.com/shootaroo/jit-grunt
    // npm install jit-grunt --save-dev
    // -------------------------------------------------------------------------
    grunt.loadNpmTasks('grunt-sass');

    require('jit-grunt')(grunt);

    /**
     * Load configuration files for Grunt
     * @param  {string} path Path to folder with tasks
     * @return {object}      All options
     */
    var loadConfig = function(path) {
        var object = {};

        try {
            var fs = require('fs'),
                key;
            fs.readdirSync(path).forEach(function(file) {
                var filePath = path + '/' + file;
                if (fs.statSync(filePath).isFile()) {
                    key = file.replace(/\.js$/, '');
                    object[key] = require(filePath)(grunt);
                }
            });
        } catch(e) {
            grunt.log.verbose.error(e.stack).or.error(e);
        }

        return object;
    };

    // Initial config
    var config = {
        pkg: grunt.file.readJSON('package.json'),
        project: {
            assets: 'skin/frontend/blugento/default',
            media: 'media'
        },
        banner: '/*! <%= pkg.name %> v<%= pkg.version %> - <%= grunt.template.today("yyyy-mm-dd HH:MM:ss") %> */',
        build: grunt.option('build') || process.env.GRUNT_BUILD || 'release'
    };

    // Load tasks options in ./grunt/tasks/options based on filesnames:
    grunt.util._.extend(config, loadConfig('./grunt/tasks/options'));

    // Initialize the configuration object
    grunt.initConfig(config);
    // grunt.log.writeln(grunt.config.get('build'));

    // Define default task
    grunt.registerTask('default', ['release', 'watch']);

    // Load tasks from ./grunt/tasks
    grunt.loadTasks('./grunt/tasks');
};
