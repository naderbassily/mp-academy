( function() {
	const openButton = document.querySelector( '.c-navicon--open[aria-controls="overlay-menu"]' );
	const closeButton = document.querySelector( '.c-navicon--close[aria-controls="overlay-menu"]' );
	const menu = document.getElementById( 'overlay-menu' );

	if ( ! openButton || ! closeButton || ! menu ) {
		return;
	}

	const focusableSelector = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

	function openMenu() {
		document.body.classList.add( 'mp-mobile-nav-open' );
		menu.classList.add( 'is-open' );
		menu.setAttribute( 'aria-hidden', 'false' );
		openButton.setAttribute( 'aria-expanded', 'true' );
		closeButton.focus();
	}

	function closeMenu() {
		document.body.classList.remove( 'mp-mobile-nav-open' );
		menu.classList.remove( 'is-open' );
		menu.setAttribute( 'aria-hidden', 'true' );
		openButton.setAttribute( 'aria-expanded', 'false' );
		openButton.focus();
	}

	openButton.addEventListener( 'click', openMenu );
	closeButton.addEventListener( 'click', closeMenu );

	menu.addEventListener( 'click', function( event ) {
		if ( event.target.closest( 'a[href]' ) ) {
			closeMenu();
		}
	} );

	document.addEventListener( 'click', function( event ) {
		if ( ! menu.classList.contains( 'is-open' ) ) {
			return;
		}

		if ( menu.contains( event.target ) || openButton.contains( event.target ) ) {
			return;
		}

		closeMenu();
	} );

	document.addEventListener( 'keydown', function( event ) {
		if ( ! menu.classList.contains( 'is-open' ) ) {
			return;
		}

		if ( event.key === 'Escape' ) {
			closeMenu();
			return;
		}

		if ( event.key !== 'Tab' ) {
			return;
		}

		const focusable = Array.from( menu.querySelectorAll( focusableSelector ) ).filter( function( element ) {
			return element.offsetParent !== null;
		} );

		if ( ! focusable.length ) {
			return;
		}

		const first = focusable[ 0 ];
		const last = focusable[ focusable.length - 1 ];

		if ( event.shiftKey && document.activeElement === first ) {
			event.preventDefault();
			last.focus();
		} else if ( ! event.shiftKey && document.activeElement === last ) {
			event.preventDefault();
			first.focus();
		}
	} );
}() );
