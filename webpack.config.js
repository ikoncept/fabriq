const path = require('path')
const CopyWebpackPlugin = require('copy-webpack-plugin')


module.exports = {
    plugins: [
        new CopyWebpackPlugin({
            patterns: [
                { from: 'resources/images', to: 'images' },
                { from: 'resources/fonts', to: 'fonts' }
            ]
        })
    ],
    resolve: {
        alias: {
            '@': path.resolve('resources/js'),
            '~': path.join(__dirname, './resources/js')
        },
    },
    module: {
        rules: [
            {
                test: /\.(postcss)$/,
                use: [
                    'vue-style-loader',
                    { loader: 'css-loader', options: { importLoaders: 1 } },
                    'postcss-loader'
                ]
            }
        ]
    },
    stats: {
        children: true
    }
}
