
// Toggle Navbar
// ---------------------------------------------------------------------------

var navbar_trigger = document.querySelector( '.trigger-region-navbar' ),
    navbar = document.querySelector( '.region-navbar' );

navbar.classList.add( 'region-navbar--inactive' );

navbar_trigger.addEventListener( 'click', function() {
  navbar.classList.toggle( 'region-navbar--inactive' );
} );
