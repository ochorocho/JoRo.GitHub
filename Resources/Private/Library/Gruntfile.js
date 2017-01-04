module.exports = function(grunt) {
	// INCLUDE TASKS PREDEFINED TASKS / MODULES
	require('load-grunt-tasks')(grunt);

	// SHOW TIME FOR EACH TASK TO FINISH - MAKE COMPILING LOOK FANCY
	require('time-grunt')(grunt);

	// CONFIGURE YOUR TASKS HERE
	grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

		// WEBFONT GENERATION
		webfont: {
		    icons: {
		        src: ['svg/*.svg'],
		        dest: '../../Public/Fonts/',
				destCss: 'tmp/',
		        options: {
					fontFilename: 'gh-icon',
					fontFamilyName: 'gh-icon',
					relativeFontPath: '../Fonts/',
				    templateOptions: {
				        baseClass: 'gh-icon',
				        classPrefix: ''
				    },
		        }
		    }
		},
			
		// SASS COMPILER
		sass: {
			dist: {
				files: {
					'tmp/Style.css': 'scss/app.scss'
				}
			}
		},

		// MINIFY GENERATED CSS FILES
		cssmin: {
			build: {
				options: {
					shorthandCompacting: true,
					roundingPrecision: -1,
				},
				files: {
					'../../Public/Styles/Style.css': ['tmp/*.css']
				}
			}
		},

		// TRIGGER TASK TO RUN ON "grunt watch"
		watch: {
			grunt: {
				files: ['Gruntfile.js']
			},
			sass: {
				files: 'scss/*.scss',
				tasks: ['webfont', 'sass', 'cssmin:build']
			},
		},
	});

	// TASK SUMMARY
	grunt.registerTask('build', ['webfont', 'sass', 'cssmin:build']);
	grunt.registerTask('default', ['build']);
};
