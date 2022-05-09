const mix = require('laravel-mix')

mix.setPublicPath('public/dist/')
mix.disableSuccessNotifications()

mix.js('resources/js/main.js', 'js/app.js').vue()

    .postCss('resources/css/app.css', 'css')
    .webpackConfig(require('./webpack.config'))
    .extract()

if (mix.inProduction()) {
    mix.version()
}
