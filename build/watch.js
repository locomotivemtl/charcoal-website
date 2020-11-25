import { buildScripts } from './scripts.js';
import { concatVendors } from './concat.js';
import { compileStyles } from './styles.js' ;
import { generateSpriteSVG } from './svgs.js';

import paths from '../mconfig.json';

// Create an named instance in one file...
import bs from 'browser-sync';

// Start the Browsersync server
bs.init({
    proxy: paths.url,
    open: false,
    notify: false
});

// and call any methods on it.
bs.watch(
    [
        paths.views.src,
        paths.scripts.dest + paths.scripts.main + '.js',
        paths.scripts.dest + paths.scripts.vendors.main + '.js',
        paths.styles.dest + paths.styles.main + '.css',
        paths.svgs.dest + 'sprite.svg'
    ]
).on('change', bs.reload);

// Build scripts and compile styles on first hit
buildScripts();
concatVendors();
compileStyles();
generateSpriteSVG();

// Watch scripts 
bs.watch(
    [
        paths.scripts.src + '**/*.js'
    ]
).on('change', () => {
    buildScripts();
});

// Watch scripts vendors
bs.watch(
    [
        paths.scripts.vendors.src + '*.js'
    ]
).on('change', () => {
    concatVendors();
});

// Watch styles
bs.watch(
    [
        paths.styles.src + '**/*.scss'
    ]
).on('change', () => {
    compileStyles();
});

// Watch svgs
bs.watch(
    [
        paths.svgs.src + '*.svg'
    ]
).on('change', () => {
    generateSpriteSVG();
});
