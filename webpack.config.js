const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  ...defaultConfig,

  entry: {
    index: path.resolve(__dirname, 'blocks/location-map/src/index.js'),
    editor: path.resolve(__dirname, 'blocks/location-map/src/style.scss'),
  },

  output: {
    path: path.resolve(__dirname, 'blocks/location-map/build'),
    filename: '[name].js',
  },

  module: {
    ...defaultConfig.module,
    rules: [
      ...defaultConfig.module.rules,
      {
        test: /\.scss$/,
        use: [MiniCssExtractPlugin.loader, 'css-loader', 'sass-loader'],
      },
    ],
  },

  plugins: [
    ...defaultConfig.plugins,
    new MiniCssExtractPlugin({
      filename: '[name].css',
    }),
  ],
};
