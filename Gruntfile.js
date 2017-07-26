var path = require('path');

module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON(path.join(__dirname, 'package.json')),
    dirs: {
      assets: 'src/Navicu/InfrastructureBundle/Resources/public/assets',
      scss: '<%= dirs.assets %>/navicu/styles',
      css: 'web/css',
      bower: 'bower_components',
    },
    bowercopy: {
      options: {
        srcPrefix: "bower_components",
        destPrefix: "src/Navicu/InfrastructureBundle/Resources/public/assets",
        runBower: true
      },
      stylesheets: {
        files: {
          "vendor/css/angular-toggle-switch-bootstrap-3.css": "angular-bootstrap-toggle-switch/style/bootstrap3/angular-toggle-switch-bootstrap-3.css",
          "vendor/css/angular-motion.css": "angular-motion/dist/angular-motion.css",
          "vendor/css/angular-material.css": "angular-material/angular-material.css",
          "vendor/css/angular-slider.css": "angularjs-slider/dist/rzslider.css",
          "vendor/css/bootstrap-select.css": "bootstrap-select/dist/css/bootstrap-select.css",
          "vendor/css/bootstrap-datepicker.css": "bootstrap-datepicker/dist/css/bootstrap-datepicker.css",
          "vendor/css/ng-table.css": "ng-table/dist/ng-table.css",
          "vendor/css/colorpicker.css": "angular-bootstrap-colorpicker/css/colorpicker.css",
          "vendor/css/select.css": "angular-ui-select/dist/select.css",
          "vendor/css/font-awesome.css": "font-awesome/css/font-awesome.css",
          "vendor/css/multirange.css": "multirange/multirange.css"
        }
      },
      folders: {
        files: {
          "vendor/sass/bootstrap-sass-official": "bootstrap-sass-official/assets/stylesheets/*",
          "vendor/sass/material-design-lite": "material-design-lite/src/*",
          "vendor/sass/font-awesome-sass": "font-awesome/scss/*.scss"
        }
      }
    },
    sass: {
      dist: {
        files: {
          '<%= dirs.css %>/web.pre.css': '<%= dirs.scss %>/main/web.scss',
          '<%= dirs.css %>/aavv.pre.css': '<%= dirs.scss %>/main/aavv.scss',
          '<%= dirs.css %>/ascribere.pre.css': '<%= dirs.scss %>/main/ascribere.scss',
          '<%= dirs.css %>/extranet.pre.css': '<%= dirs.scss %>/main/extranet.scss',
          '<%= dirs.css %>/admin.pre.css': '<%= dirs.scss %>/main/admin.scss',
        }
      }
    },
    concat: {
      web: {
        src: [
          '<%= dirs.css %>/web.pre.css',
          '<%= dirs.assets %>/vendor/css/*.css',
        ],
        dest: '<%= dirs.css %>/web.css',
      },
      extranet: {
        src: [
          '<%= dirs.css %>/extranet.pre.css',
          '<%= dirs.assets %>/vendor/css/*.css',
        ],
        dest: '<%= dirs.css %>/extranet.css',
      },
      ascribere: {
        src: [
          '<%= dirs.css %>/ascribere.pre.css',
          '<%= dirs.assets %>/vendor/css/*.css',
        ],
        dest: '<%= dirs.css %>/ascribere.css',
      },
      aavv: {
        src: [
          '<%= dirs.css %>/aavv.pre.css',
          '<%= dirs.assets %>/vendor/css/*.css',
        ],
        dest: '<%= dirs.css %>/aavv.css',
      },
      admin: {
        src: [
            '<%= dirs.css %>/admin.pre.css',
            '<%= dirs.assets %>/vendor/css/*.css',
        ],
        dest: '<%= dirs.css %>/admin.css',
      },
    },
    postcss: {
      options: {
        processors: [
          require('autoprefixer')({
            browsers: ['> 5%', 'ie > 7'],
          }),
        ]
      },
      dist: {
        src: [
          '<%= dirs.css %>/*.css',
          '!<%= dirs.css %>/*.min.css',
          '!<%= dirs.css %>/*.pre.css',
        ],
      }
    },
    cssmin: {
      dist: {
        options: {
          //advanced: false,
          processImportFrom: ['local'],
          aggressiveMerging: false,
          restructuring: false,
          shorthandCompacting: false,
          roundingPrecision: -1,
        },
        files: [{
          expand: true,
          cwd: 'web/css',
          src: [
            '*.css',
            '!*.min.css',
            '!*.pre.css',
          ],
          dest: 'web/css',
          ext: '.min.css',
        }],
      },
    },
    webfont: {
      icons: {
        src: '<%= dirs.assets %>/navicu/icons/*.svg',
        dest: 'web/fonts/font-navicu',
        destCss: '<%= dirs.scss %>/navicu-sass-official/external',
        options: {
          htmlDemo: false,
          fontFilename: 'navicu_icons',
          stylesheet: 'scss',
          optimize: false,
          autoHint: false,
          fontFamilyName: 'icons',
          template: '<%= dirs.assets %>/navicu/icon-template.scss',
          relativeFontPath: '/fonts/font-navicu',
        }
      }
    },
    watch: {
      styles: {
        files: [
          '<%= dirs.scss %>/**/*.scss',
        ],
        tasks: ['sass', 'concat', 'postcss'],
        options: {
          interrupt: true,
          atBegin: true,
        },
      },
      fonts: {
        files: [
          '<%= dirs.assets %>/navicu/icons',
          '<%= dirs.assets %>/navicu/icons/*.svg',
        ],
        tasks: ['fonts'],
        options: {
          interrupt: false,
          atBegin: false,
        },
      },
    },
  });

  grunt.loadNpmTasks("grunt-bowercopy");
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-postcss');
  grunt.loadNpmTasks('grunt-webfont');

  grunt.registerTask('scss', ['sass']);

  grunt.registerTask('font', ['webfont']);
  grunt.registerTask('fonts', ['font']);

  grunt.registerTask('css', [
    'bowercopy',
    'sass',
    'concat',
    'postcss',
    'cssmin',
  ]);

  grunt.registerTask('default', [
    'font',
    'css',
  ]);
};
