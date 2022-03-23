const mix = require('laravel-mix');
const TsconfigPathsPlugin = require('tsconfig-paths-webpack-plugin');
/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

const isProduction = process.env.NODE_ENV === "production";

if (!isProduction) mix.sourceMaps();



mix.ts('resources/js/app.ts', 'public/js')
    // .ts('resources/js/api.ts', 'public/js')
    .sass('resources/scss/app.scss', 'public/css/app.css')
    .vue()
    .copy('node_modules/@coreui/coreui/dist/css/coreui.min.css', 'public/css')
    .webpackConfig({
        module: {
            rules: [
                {
                    test: /\.pug$/,
                    oneOf: [
                        {
                            resourceQuery: /^\?vue/,
                            use: ['pug-plain-loader']
                        },
                        {
                            use: ['raw-loader', 'pug-plain-loader']
                        }
                    ]
                }
            ]
        },
        resolve: {
            plugins: [new TsconfigPathsPlugin()]
        }
    })

