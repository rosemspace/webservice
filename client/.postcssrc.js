let path = require('path');

module.exports = {
    parser: 'postcss-scss',
    plugins: [
        require('postcss-import'),
        require('postcss-nesting'),
        require('postcss-url')({
            from: path.resolve(__dirname, 'src/rosem/css/style.css'),
            to: path.resolve(__dirname, '../webservice/public/css/style.css')
        }),
        require('postcss-apply'),
        require('css-mqpacker'),
        require('postcss-custom-properties'),
        require('postcss-calc'),
        require('postcss-color-function'),
        require('postcss-custom-media'),
        require('postcss-media-minmax'),
        require('postcss-custom-selectors'),
        require('postcss-selector-not'),
        require('postcss-selector-matches'),
        // require('postcss-smart-import')({ /* ...options */ }),
        // require('precss')({ /* ...options */ }),
        // require('autoprefixer'),
        // require('cssnano')({
        //     zindex: false,
        //     discardComments: {
        //         removeAll: true
        //     }
        // }),
    ]
};
