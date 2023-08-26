// -----------------------------------------------------------------------------
// @see: https://github.com/sasstools/grunt-sass-lint
// npm install grunt-sass-lint --save-dev
// -----------------------------------------------------------------------------

module.exports = function(grunt) {
    return {
        options: {
            configFile: './grunt/.sass-lint.yml',
            formatter: 'html',
            outputFile: 'sass-lint-report.html'
        },
        dev: [
            '<%= project.assets %>/src/scss/component/_*.scss',
            '<%= project.assets %>/src/scss/module/_*.scss'
        ]
    };
};
