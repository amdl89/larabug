const mix = require("laravel-mix");
const path = require("path");

mix.alias({ ziggy: path.resolve("vendor/tightenco/ziggy/dist.vue") });

const VuetifyLoaderPlugin = require("vuetify-loader/lib/plugin");

mix.js("resources/src/app.js", "public/js")
    .sass("resources/src/Sass/app.scss", "public/css")
    .vue(2)
    .version()
    .alias({ "@": "resources/src" })
    .webpackConfig({
        plugins: [new VuetifyLoaderPlugin()],
    });

mix.disableSuccessNotifications();

mix.browserSync({
    open: "external",
    host: process.env.APP_DOMAIN_NAME,
    proxy: process.env.APP_URL,
    https: {
        key: process.env.SSL_KEY_PATH,
        cert: process.env.SSL_CERT_PATH,
    },
    browser: "chrome",
});
