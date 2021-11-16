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

	var userConfig = {
		buildDir: "public",
		srcDir: "www"
	};

	var taskConfig = {

		copy: {
			assets: {
				files: [
					{
						src: ["**"],
						dest: "<%= buildDir %>/images/",
						cwd: "<%= srcDir %>/images/",
						expand: true
					}
				]
			}
		},
		sass: {
			compile: {
				files: {
					"<%= buildDir %>/css/lbcg-binco-card-generator.css": "<%= srcDir %>/sass/main.scss"
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
				src: "<%= buildDir %>/css/lbcg-binco-card-generator.css"
			}
		},
		cssmin: {
			target: {
				files: [{
					expand: true,
					cwd: "<%= buildDir %>/css/",
					src: ['*.css', '!*.min.css'],
					dest: "<%= buildDir %>/css/",
					ext: ".min.css"
				}]
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
				tasks: ["sass:compile", "cssmin", "postcss:dist"],
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
		"copy:assets"
	]);

	grunt.registerTask("default", ["sass:compile", "postcss:dist"]);

};