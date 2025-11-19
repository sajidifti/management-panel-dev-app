module.exports = {
  darkMode: 'class',
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './resources/css/**/*.css',
    // include main app resources for local dev when running from repository root
    '../../resources/views/**/*.blade.php',
    '../../resources/js/**/*.js',
    '../../resources/css/**/*.css'
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}
