const EncoreModule = require('@symfony/webpack-encore');

const Encore = EncoreModule.default || EncoreModule;

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.mjs')
    .addEntry('script_autor', './assets/js/autor/script.js') //scripts da pagina autor
    .addEntry('script_assunto', './assets/js/assunto/script.js') //scripts da pagina assunto
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