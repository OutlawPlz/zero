
( function ( Drupal ) {

  'use strict';

  var element = document.querySelector( '.row-navbar' );

  new Droppy( element, {
    parentSelector: '.wrapper-navbar',
    triggerSelector: '.trigger-navbar',
    dropdownSelector: '.region-navbar',
    animationIn: 'animation-in',
    animationOut: 'animation-out'
  } );

} ( Drupal ) );
