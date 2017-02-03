( function () {

  'use strict';

    var navbar = document.querySelector('.row-navbar'),
        previewbar = document.querySelector('.node-preview-container'),
        pageWrapper = document.querySelector('.wrapper-page');

    if (previewbar) {
      pageWrapper.style.marginTop = navbar.offsetHeight + previewbar.offsetHeight + 'px';
      navbar.style.marginTop = previewbar.offsetHeight + 'px';
    }
    else {
      pageWrapper.style.marginTop = navbar.offsetHeight + 'px';
    }

    navbar.classList.add('row-navbar--fixed');

} () );
