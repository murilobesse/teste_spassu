const EncoreModule = require('@symfony/webpack-encore');

const Encore = EncoreModule.default || EncoreModule;

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.mjs')
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .enableSassLoader()
    .configureBabel((config) => {
        config.sourceType = 'unambiguous';
    })
;

module.exports = Encore.getWebpackConfig();