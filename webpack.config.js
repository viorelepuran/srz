const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  ...defaultConfig,
  entry: {
    // Block JavaScript
    'location-map/index': path.resolve(__dirname, 'blocks/location-map/src/index.js'),
    // Editor styles
    'location-map/editor': path.resolve(__dirname, 'blocks/location-map/src/style.scss'),
  },
  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: (pathData) => {
      const [block, file] = pathData.chunk.name.split('/');
      return `../blocks/${block}/build/${file}.js`;
    },
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
      filename: (pathData) => {
        const [block, file] = pathData.chunk.name.split('/');
        return `../blocks/${block}/build/${file}.css`;
      },
    }),
  ],
};
