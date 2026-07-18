const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

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
        plugins: [
            new MiniCssExtractPlugin({
                filename: 'css/app.css'
            })
        ]
    }
}