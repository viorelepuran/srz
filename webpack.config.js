const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
  ...defaultConfig,
  entry: {
    // Block: location-map
    'location-map/index': path.resolve(__dirname, 'blocks/location-map/src/index.js'),
  },
  output: {
    // ✅ Output to a safe temporary directory
    path: path.resolve(__dirname, 'dist'),

    // ✅ Send final output to each block's build folder
    filename: (pathData) => {
      const [block, file] = pathData.chunk.name.split('/');
      return `../blocks/${block}/build/${file}.js`;
    }
  }
};
