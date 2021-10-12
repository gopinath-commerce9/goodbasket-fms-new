const dotenvExpand = require('dotenv-expand');
dotenvExpand(require('dotenv').config({ path: '../../.env'/*, debug: true*/}));

const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');

let publicMainAssets = './../../public';
let moduleAssetDir = __dirname + '/Resources/assets';
mix.setPublicPath(publicMainAssets).mergeManifest();

mix.copyDirectory(moduleAssetDir + '/backend', publicMainAssets + '/backend');
mix.copyDirectory(moduleAssetDir + '/frontend', publicMainAssets + '/frontend');

/*mix.js(__dirname + '/Resources/assets/js/app.js', 'js/base.js')
    .sass( __dirname + '/Resources/assets/sass/app.scss', 'css/base.css');*/

if (mix.inProduction()) {
    mix.version();
}
