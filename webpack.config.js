const path = require('path');

const glob = require('glob').sync.bind(require('glob'));
const webpack = require('webpack');

const CleanPlugin = require('clean-webpack-plugin');
const ngAnnotatePlugin = require('ng-annotate-webpack-plugin');
const AssetsPlugin = require('assets-webpack-plugin');

const PREFIX = path.join(__dirname, 'src/Navicu/InfrastructureBundle/Resources/public/assets/navicu/angular');
const BOWER = path.join(__dirname, 'bower_components');
const NODE_MODULES = path.join(__dirname, 'node_modules');

const PUBLIC = path.join(__dirname, 'web');
const DIR_BUILDS = path.join(PUBLIC, 'builds');

// var PUBLIC = path.join(__dirname, 'src/Navicu/InfrastructureBundle/Resources/public');
// var DIR_BUILDS = path.join(PUBLIC, 'assets/navicu/dist/angular-apps/');

let PUBLICPATH = path.relative(PUBLIC, DIR_BUILDS);
if (PUBLICPATH.indexOf('/') !== 0) {
    PUBLICPATH = `/${PUBLICPATH}`;
}
if (PUBLICPATH.lastIndexOf('/') !== PUBLICPATH.length - 1) {
    PUBLICPATH += '/';
}

const production = process.env.NODE_ENV === 'production';

const assetsPluginInstance = new AssetsPlugin({
    path: path.join(__dirname, 'web'),
    prettyPrint: true,
    assetsRegex: /\.(jpe?g|png|gif|svg)\?./i,
});

let plugins = [

    new webpack.optimize.CommonsChunkPlugin({
        name: 'vendor',
        minChunks: 3,
        chunks: [
            'aavv',
            'ascribere',
            'client',
            'extranet',
        ],
    }),

    new ngAnnotatePlugin({
        add: true,
        // other ng-annotate options here
    }),

    new (require('webpack-assets-manifest'))({
        output: 'assets-manifest.json',
    }),

    new (require('prefetch-context-webpack-plugin').default)({
        context: path.join(PREFIX, 'AAVV'),
        loader: 'eslint-loader?configFile=.eslintrc.js',
    }),

    new (require('prefetch-context-webpack-plugin').default)({
        context: path.join(PREFIX, 'admin'),
        loader: 'eslint-loader?configFile=.eslintrc.js',
    }),

    new (require('prefetch-context-webpack-plugin').default)({
        context: path.join(PREFIX, 'vendor'),
        loader: 'eslint-loader?configFile=.eslintrc.js',
    }),

].concat(production ? [] : [
    assetsPluginInstance,
]);

if (production) {
    plugins = plugins.concat([

        // Cleanup the DIR_BUILDS folder before
        // compiling our final assets
        new CleanPlugin(DIR_BUILDS),

        // This plugin looks for similar chunks and files
        // and merges them for better caching by the user
        new webpack.optimize.DedupePlugin(),

        // This plugins optimizes chunks and modules by
        // how much they are used in your app
        new webpack.optimize.OccurenceOrderPlugin(),

        // This plugin prevents Webpack from creating chunks
        // that would be too small to be worth loading separately
        new webpack.optimize.MinChunkSizePlugin({
            minChunkSize: 50 * 1024, // Bytes.
        }),

        // This plugin minifies all the Javascript code of the final bundle
        new webpack.optimize.UglifyJsPlugin({
            mangle: false,
            sourceMap: true,
            compress: {
                drop_console: true,
                warnings: false,
            },
        }),

        // This plugins defines various variables that we can set to false
        // in production to avoid code related to them from being compiled
        // in our final bundle
        new webpack.DefinePlugin({
            __SERVER__: !production,
            __DEVELOPMENT__: !production,
            __DEVTOOLS__: !production,
            'process.env': {
                BABEL_ENV: JSON.stringify(process.env.NODE_ENV),
            },
        }),

        assetsPluginInstance,
    ]);
}

module.exports = {
    devtool: production ? 'source-map' : 'cheap-module-source-map',
    entry: {
        client: ['babel-polyfill', 'client/index.js'],
        extranet: ['babel-polyfill', 'extranet/index.js'],
        ascribere: ['babel-polyfill', 'ascribere/index.js'],
        admin: ['babel-polyfill', 'admin/index.js'],
        aavv: ['babel-polyfill', 'AAVV/index.js'],
    },
    output: {
        path: DIR_BUILDS,
        filename: production ? '[name].bundle.[hash].js' : '[name].bundle.js',
        chunkFilename: production ? '[id].bundle.[hash].js' : '[id].bundle.js',
        publicPath: PUBLICPATH,
    },
    module: {
        noParse: glob(path.join(BOWER, '**/*')),
        loaders: [
            {
                test: /[\/\\]bower_components[\/\\]bootstrap-select[\/\\]dist[\/\\]js[\/\\]bootstrap-select\.js$/,
                loader: 'imports?jquery=jquery',
            },
            {
                test: /[\/\\]bower_components[\/\\]angularjs\-slider[\/\\].+/,
                loader: 'imports?angular=angular',
            },
            {
                test: /\.js/,
                exclude: /node_modules/,
                loader: 'babel',
                include: PREFIX,
            },
            {
                test: /\/AAVV\/.+\.js$/,
                loader: 'eslint',
                exclude: /node_modules/,
            },
//            {
//                test: /\.(png|jpe?g|gif)$/,
//                loaders: [
//                    'url?limit=4096!image-webpack?bypassOnDebug&optimizationLevel=7&interlaced=true',
//                ],
//            },
            {
                test: /\.(jpe?g|png|gif|svg)$/i,
                loader: `file?name=[path][name].[ext]?[hash]`,
            },
            {
                test: /\.html$/,
                loader: 'html',
                query: {
                    minimize: true
                }
            },
        ],
    },
    htmlLoader: {
        attrs: false,
    },
    plugins: plugins,
    resolve: {
        root: [
            PREFIX,
            NODE_MODULES,
            BOWER,
            PUBLIC,
        ],
        extensions: ['', '.js'],
    },
    watch: process.argv.some((flag) => {
        return flag.indexOf('watch') >= 0;
    }),
};
