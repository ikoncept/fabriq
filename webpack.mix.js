const mix = require('laravel-mix')

const ImageminPlugin = require('imagemin-webpack-plugin').default
const CopyWebpackPlugin = require('copy-webpack-plugin')
const path = require('path')
require('laravel-mix-bundle-analyzer')
require('laravel-mix-tailwind')

// Image minifictaion
mix.webpackConfig({
    plugins: [
        new CopyWebpackPlugin({
            patterns: [
                { from: 'resources/images', to: 'images' }
            ]
        }),
        new ImageminPlugin({
            test: /\.(jpe?g|png|gif|svg)$/i
        })
    ],
    module: {
        // rules: [
        //     {
        //         test: /\.postcss$/,
        //         use: ['style-loader', 'postcss-loader']
        //     }
        // ]
        // rules: [
        //     {
        //         // test: /\.postcss$/i,
        //         test: /\.(postcss)$/,
        //         // use: ['style-loader', 'css-loader', 'postcss-loader']
        //         use: [
        //             'vue-style-loader',
        //             { loader: 'css-loader', options: { importLoaders: 1 } },
        //             'postcss-loader'
        //         ]
        //     }
        // ]
        rules: [
            {
                test: /\.postcss$/,
                use: [
                    'vue-style-loader',
                    'css-loader',
                    {
                        loader: 'postcss-loader'
                    }
                ]
            }
        ]
    },
    resolve: {
        extensions: ['.js', '.json', '.vue'],
        alias: {
            '~': path.join(__dirname, './resources/js')
        }
    }
})
mix.setPublicPath('public/dist/')
mix.disableSuccessNotifications()

mix.js('resources/js/main.js', 'js/app.js').vue()
    .postCss('resources/css/app.css', 'css', [
        require('postcss-nested'),
        require('tailwindcss')('./tailwind.config.js')
    ])
    .tailwind()
    .extract()

if (mix.inProduction()) {
    mix.version()
}
