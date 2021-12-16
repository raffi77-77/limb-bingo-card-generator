module.exports = function (grunt) {

    /**
     * Load required Grunt tasks. These are installed based on the versions listed
     * in `package.json` when you do `npm install` in this directory.
     */

    grunt.loadNpmTasks("grunt-contrib-sass");
    grunt.loadNpmTasks("grunt-contrib-watch");
    grunt.loadNpmTasks("grunt-postcss");
    grunt.loadNpmTasks("grunt-contrib-copy");
    grunt.loadNpmTasks("grunt-contrib-cssmin");
    grunt.loadNpmTasks("grunt-contrib-uglify");

    var userConfig = {
        buildPubDir: "public",
        buildAdmDir: "admin",
        srcDir: "www"
    };

    var taskConfig = {

        copy: {
            assets: {
                files: [
                    {
                        src: ["**"],
                        dest: "<%= buildPubDir %>/images/",
                        cwd: "<%= srcDir %>/images/",
                        expand: true
                    }
                ]
            }
        },
        sass: {
            compile: {
                files: {
                    "<%= buildPubDir %>/css/lbcg-public.css": "<%= srcDir %>/sass/main.scss"
                }
            }
        },
        postcss: {
            options: {
                processors: [
                    require("autoprefixer")({
                        browsers: "last 5 versions"
                    })
                ]
            },
            dist: {
                src: "<%= buildPubDir %>/css/lbcg-public.css"
            }
        },
        cssmin: {
            target: {
                files: [{
                    expand: true,
                    cwd: "<%= buildPubDir %>/css/",
                    src: ['*.css', '!*.min.css'],
                    dest: "<%= buildPubDir %>/css/",
                    ext: ".min.css"
                }]
            }
        },
        uglify: {
            target: {
                files: [{
                    expand: true,
                    cwd: "<%= buildPubDir %>/js/",
                    src: ['*.js', '!*.min.js'],
                    dest: "<%= buildPubDir %>/js/",
                    ext: ".min.js"
                },
                {
                    expand: true,
                    cwd: "<%= buildAdmDir %>/js/",
                    src: ['*.js', '!*.min.js'],
                    dest: "<%= buildAdmDir %>/js/",
                    ext: ".min.js"
                }],
            }
        },
        delta: {
            options: {
                livereload: false
            },

            /**
             * When the SCSS files change, we need to compile and copy to build dir
             */
            sass: {
                files: ["<%= srcDir %>/**/*.scss"],
                tasks: ["sass:compile", "cssmin", "uglify", "postcss:dist"],
                options: {
                    livereload: true
                }
            },

            assets: {
                files: [
                    "<%= srcDir %>/images/**/*",
                ],
                tasks: ["copy:assets"]
            }
        }
    };

    grunt.initConfig(grunt.util._.extend(taskConfig, userConfig));
    // grunt.config.init(taskConfig);

    grunt.renameTask("watch", "delta");
    grunt.registerTask("watch", [
        "sass:compile",
        "cssmin",
        "copy:assets",
        "postcss:dist",
        "delta"
    ]);

    grunt.registerTask("build", [
        "sass:compile",
        "postcss:dist",
        "cssmin",
        "uglify",
        "copy:assets"
    ]);

    grunt.registerTask("default", ["sass:compile", "postcss:dist"]);

};