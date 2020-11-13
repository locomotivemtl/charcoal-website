import svg4everybody from 'svg4everybody';
import { html } from './utils/environment';
import smoothscroll from 'smoothscroll-polyfill';

export default function() {
    svg4everybody();

    const isMobile = (/Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) || (navigator.platform === 'MacIntel' && navigator.maxTouchPoints > 1);

    if(isMobile) {
        html.classList.add('is-mobile');
    }

    window.isMobile = isMobile;

    smoothscroll.polyfill();
}
