document.addEventListener( 'DOMContentLoaded', function () {
	var toggle = document.querySelector( '.menu-toggle' );
	var menu = document.querySelector( '.primary-menu' );

	if ( ! toggle || ! menu ) {
		return;
	}

	toggle.addEventListener( 'click', function () {
		menu.classList.toggle( 'is-open' );
	} );
} );
