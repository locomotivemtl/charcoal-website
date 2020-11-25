import mixer from 'svg-mixer';
import paths from '../mconfig.json';
import message from './utils/message.js';

export function generateSpriteSVG() {
    message('Generating SVG sprite ...', 'success');
    // Write sprite content on disk
    mixer([paths.svgs.src + '*.svg'])
    .then(result => result.write(paths.svgs.dest + 'sprite.svg'));
}