require('./bootstrap');

import Alpine from 'alpinejs';
import 'tw-elements';

try {
    window.$ = window.jQuery = require('jquery');
    window.Alpine = Alpine;
} catch (e){}

Alpine.start();
