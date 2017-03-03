( function ( Drupal ) {

  Drupal.behaviors.adaptiveNavbar = {
    attach: function ( context, settings ) {

      var element = context.querySelector('.row-navbar');

      new Droppy( element, {
        dropdownSelector: '.region-navbar',
        triggerSelector: '.trigger-navbar',
        parentSelector: '.wrapper-navbar',
        animationIn: 'animation-in',
        animationOut: 'animation-out'
      } );
    }
  }

} ( Drupal ) );