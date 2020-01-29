/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you require will output into a single css file (app.css in this case)
require('../css/app.scss');

// Need jQuery? Install it with "yarn add jquery", then uncomment to require it.
const $ = require('jquery');

// Create global $ and jQuery variables
global.$ = global.jQuery = $;

require('bootstrap');
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');


/*$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});

$(document).on('change', '.custom-file-input', function () {
    let fileName = $(this).val().replace(/\\/g, '/').replace(/.*\//, '');
    $(this).parent('.custom-file').find('.custom-file-label').text(fileName);
});

function changePlaceholder(change = 1){
    if(change==1){
        document.getElementById('search-header').placeholder = 'RECHERCHER...';
    }else{
        document.getElementById('search-header').placeholder = '';
    }
}

if(window.innerWidth>=370){
    changePlaceholder(1); 
}else{
    changePlaceholder(0);
}

window.onresize = function(){
    if(window.innerWidth>=370){
        changePlaceholder(1); 
    }else{
        changePlaceholder(0);
    }
}*/