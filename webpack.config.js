const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserPlugin = require('terser-webpack-plugin'); //agrego por que en DomCloud tiene un limite de memoria y no me deja compilar el js, por lo que agrego este plugin para minificarlo y que ocupe menos memoria

module.exports = (env, argv) =>{

    const isProduction = argv.mode === 'production';

    return {
        entry: path.resolve(__dirname, 'resources/js/app.js'),
        output: {
            path: path.resolve(__dirname, 'public/build'),
            filename: 'js/app.js',
            clean: true,
        },
        devtool: isProduction ? false : 'source-map',
        module: {
            rules: [
                {
                    test: /\.css$/i,
                    use:[MiniCssExtractPlugin.loader, 'css-loader', 'postcss-loader'],
                }
            ]
        },
        optimization: {
            minimize: isProduction,
            minimizer: [
                new TerserPlugin({
                    parallel: false,
                }),
            ],
        },
        plugins: [
            new MiniCssExtractPlugin({
                filename: 'css/app.css'
            })
        ]
    }
}