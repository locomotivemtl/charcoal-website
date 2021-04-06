import paths from '../mconfig.json';
import message from './utils/message.js';
import fs from 'fs';
import concat from 'concat';

export function concatVendors() {
    // Get all files in scripts/vendors/
    const files = fs.readdirSync(paths.scripts.vendors.src);

    // Extract no-JS files
    var jsFiles = files.filter(file => file.includes('.js'));

    // Add absolute path
    jsFiles = jsFiles.map(file => `${paths.scripts.vendors.src + file}`);

    // Add extra files from node_modules
    jsFiles.push('node_modules/gsap/dist/gsap.min.js');
    jsFiles.push('node_modules/swiper/swiper-bundle.min.js');

    message('Concat vendors...', 'success');

    concat(jsFiles, paths.scripts.dest + paths.scripts.vendors.main + '.js')
}
