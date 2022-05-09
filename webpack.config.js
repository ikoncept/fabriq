const path = require('path')

module.exports = {
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
        children: false
    }
}
