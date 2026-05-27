( function() {
	function initMobileMenu() {
		const openButton = document.querySelector( '.c-navicon--open' );
		const closeButton = document.querySelector( '.c-navicon--close' );
		const overlayMenu = document.getElementById( 'overlay-menu' );

		if ( ! openButton || ! closeButton || ! overlayMenu ) {
			return;
		}

		let backdrop = document.querySelector( '.c-header__overlay-backdrop' );

		if ( ! backdrop ) {
			backdrop = document.createElement( 'div' );
			backdrop.className = 'c-header__overlay-backdrop';
			backdrop.setAttribute( 'aria-hidden', 'true' );
			document.body.appendChild( backdrop );
		}

		const setMenuState = function( isOpen ) {
			overlayMenu.classList.toggle( 'is-open', isOpen );
			overlayMenu.setAttribute( 'aria-hidden', isOpen ? 'false' : 'true' );
			openButton.setAttribute( 'aria-expanded', isOpen ? 'true' : 'false' );
			document.body.classList.toggle( 'has-overlay-menu', isOpen );
		};

		openButton.addEventListener( 'click', function( event ) {
			event.preventDefault();
			setMenuState( true );
		} );

		closeButton.addEventListener( 'click', function( event ) {
			event.preventDefault();
			setMenuState( false );
		} );

		backdrop.addEventListener( 'click', function() {
			setMenuState( false );
		} );

		document.addEventListener( 'keydown', function( event ) {
			if ( event.key === 'Escape' && overlayMenu.classList.contains( 'is-open' ) ) {
				setMenuState( false );
			}
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initMobileMenu );
	} else {
		initMobileMenu();
	}
}() );
