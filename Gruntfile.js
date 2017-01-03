module.exports = function(grunt) {


    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        checktextdomain: {
            options:{
                text_domain: 'convertkit',
                keywords: [
                    '__:1,2d',
                    '_e:1,2d',
                    '_x:1,2c,3d',
                    'esc_html__:1,2d',
                    'esc_html_e:1,2d',
                    'esc_html_x:1,2c,3d',
                    'esc_attr__:1,2d',
                    'esc_attr_e:1,2d',
                    'esc_attr_x:1,2c,3d',
                    '_ex:1,2c,3d',
                    '_n:1,2,4d',
                    '_nx:1,2,4c,5d',
                    '_n_noop:1,2,3d',
                    '_nx_noop:1,2,3c,4d'
                ]
            },
            files: {
                src:  [
                    '**/*.php', // Include all files
                    '!node_modules/**', // Exclude node_modules/
                    '!tests/**', // Exclude tests/
                    '!vendor/**' // Exclude vendor libraries
                ],
                expand: true
            }
        },
        makepot: {
            options: {
                type: 'wp-plugin',
                domainPath: 'languages',
                mainFile: 'wp-convertkit.php',
                potHeaders: {
                    poedit: true,
                    'report-msgid-bugs-to': 'https://github.com/convertkit/ConvertKit-WordPress/issues',
                    'language-team': 'ConvertKit <support@convertkit.com>',
                    'language': 'en_US'
                }
            },
            frontend: {
                options: {
                    potFilename: 'convertkit.pot',
                    exclude: [
                        'tests/.*',
                        'woo-includes/.*',
                        'includes/libraries/.*',
                        'node_modules',
                        'tmp'
                    ]
                }
            }
        },
        potomo: {
            dist: {
                options: {
                    poDel: false
                },
                files: [{
                    expand: true,
                    cwd: 'languages',
                    src: ['*.po'],
                    dest: 'languages',
                    ext: '.mo',
                    nonull: false
                }]
            }
        }
    });

    // Set the default grunt command to run test cases
    grunt.registerTask('default', []);

    /**
     * Run i18n related tasks. This includes extracting translatable strings, uploading the master
     * pot file, downloading any and all 100% complete po files, converting those to mo files.
     * If this is part of a deploy process, it should come before zipping everything up
     */
    grunt.registerTask( 'i18n', [
        'checktextdomain',
        'makepot'
    ]);

    // Load checktextdomain
    grunt.loadNpmTasks( 'grunt-checktextdomain' );
    // Load the i18n plugin to extract translatable strings
    grunt.loadNpmTasks( 'grunt-wp-i18n' );
    // Load potomo plugin
    grunt.loadNpmTasks( 'grunt-potomo' );
};