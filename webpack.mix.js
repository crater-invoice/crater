const mix = require('laravel-mix')

mix.webpackConfig({
  resolve: {
    alias: {
      '@': path.resolve(
        __dirname,
        'resources/assets/js'
      )
    }
  }
})

/*
 |--------------------------------------------------------------------------
 | Admin
 |--------------------------------------------------------------------------
 */

mix.js('resources/assets/js/app.js', 'public/assets/js/')
  .sass('resources/assets/sass/crater.scss', 'public/assets/css/')

if (!mix.inProduction()) {
  mix.webpackConfig({
    devtool: 'source-map'
  }).sourceMaps()
} else {
  mix.version()
}
