( function () {

  'use strict';

    var navbar = document.querySelector('.row-navbar'),
        navbarHeight = navbar.offsetHeight,
        pageWrapper = document.querySelector('.wrapper-page');

    navbar.classList.add('row-navbar--fixed');
    pageWrapper.style.marginTop = navbarHeight + 'px';

} () );
