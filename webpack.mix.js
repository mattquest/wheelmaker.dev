const mix = require("laravel-mix")

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js("resources/js/app.js", "public/js").vue().sass("resources/sass/app.scss", "public/css")

// Get the APP_URL from .env and remove protocol
let url = process.env.APP_URL.replace(/(^\w+:|^)\/\//, "")
mix.options({
  hmrOptions: {
    host: url,
    port: 8080 // Can't use 443 here because address already in use
  }
})
