/**
 * XPERTZ global navigation and commerce interactions.
 */
( () => {
	'use strict';

	const config = window.xpertzCommerce || {};
	const doc = document;
	let activeLayer = null;
	let previousFocus = null;

	const escapeSelector = ( value ) => {
		if ( window.CSS && typeof window.CSS.escape === 'function' ) {
			return window.CSS.escape( value );
		}
		return String( value ).replace( /[^a-zA-Z0-9_-]/g, '\\$&' );
	};

	const focusable = ( container ) => Array.from(
		container.querySelectorAll( 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])' )
	).filter( ( element ) => ! element.hidden && element.offsetParent !== null );

	const setPageLock = () => {
		const hasOpenLayer = doc.querySelector( '.xhc-mobile-drawer.is-open, .xhc-search-dialog:not([hidden])' );
		doc.body.classList.toggle( 'xhc-layer-open', Boolean( hasOpenLayer ) );
	};

	const toast = ( message, isError = false ) => {
		let region = doc.querySelector( '.xhc-toast-region' );
		if ( ! region ) {
			region = doc.createElement( 'div' );
			region.className = 'xhc-toast-region';
			region.setAttribute( 'aria-live', 'polite' );
			doc.body.append( region );
		}
		const item = doc.createElement( 'div' );
		item.className = `xhc-toast${ isError ? ' is-error' : '' }`;
		item.setAttribute( 'role', isError ? 'alert' : 'status' );
		item.textContent = message;
		region.append( item );
		window.setTimeout( () => item.remove(), 4200 );
	};

	const closePopovers = ( except = '' ) => {
		doc.querySelectorAll( '[data-popover]' ).forEach( ( panel ) => {
			if ( panel.dataset.popover === except ) {
				return;
			}
			panel.hidden = true;
			const toggle = doc.querySelector( `[data-popover-toggle="${ escapeSelector( panel.dataset.popover ) }"]` );
			if ( toggle ) {
				toggle.setAttribute( 'aria-expanded', 'false' );
			}
		} );
	};

	const togglePopover = ( toggle ) => {
		const name = toggle.dataset.popoverToggle;
		const panel = doc.querySelector( `[data-popover="${ escapeSelector( name ) }"]` );
		if ( ! panel ) {
			return;
		}
		const willOpen = panel.hidden;
		closePopovers( willOpen ? name : '' );
		panel.hidden = ! willOpen;
		toggle.setAttribute( 'aria-expanded', String( willOpen ) );
		if ( willOpen && name === 'notifications' && config.isLoggedIn ) {
			const form = new URLSearchParams( {
				action: 'xpertz_mark_notifications',
				nonce: config.notificationsNonce || '',
			} );
			window.fetch( config.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: form } ).catch( () => {} );
			const badge = toggle.querySelector( '.xhc-count' );
			if ( badge ) {
				badge.remove();
			}
		}
	};

	doc.addEventListener( 'click', ( event ) => {
		const toggle = event.target.closest( '[data-popover-toggle]' );
		if ( toggle ) {
			event.preventDefault();
			event.stopPropagation();
			togglePopover( toggle );
			return;
		}
		if ( ! event.target.closest( '[data-popover]' ) ) {
			closePopovers();
		}
	} );

	const giftDialog = doc.querySelector( '[data-gift-course-dialog]' );
	const giftForm = doc.querySelector( '[data-gift-course-form]' );
	const openGiftDialog = () => {
		if ( ! giftDialog ) {
			return;
		}
		previousFocus = doc.activeElement;
		giftDialog.hidden = false;
		activeLayer = giftDialog;
		setPageLock();
		window.setTimeout( () => giftDialog.querySelector( 'input' )?.focus(), 20 );
	};
	const closeGiftDialog = () => {
		if ( ! giftDialog || giftDialog.hidden ) {
			return;
		}
		giftDialog.hidden = true;
		activeLayer = null;
		setPageLock();
		previousFocus?.focus?.();
	};

	doc.addEventListener( 'click', ( event ) => {
		if ( event.target.closest( '[data-gift-course-open]' ) ) {
			event.preventDefault();
			openGiftDialog();
		}
		if ( event.target.closest( '[data-gift-course-close]' ) ) {
			event.preventDefault();
			closeGiftDialog();
		}
	} );

	if ( giftForm ) {
		giftForm.addEventListener( 'submit', async ( event ) => {
			event.preventDefault();
			const submit = giftForm.querySelector( '[type="submit"]' );
			const data = new FormData( giftForm );
			submit.disabled = true;
			submit.setAttribute( 'aria-busy', 'true' );
			try {
				const response = await window.fetch( config.ajaxUrl, {
					method: 'POST',
					credentials: 'same-origin',
					body: new URLSearchParams( {
						action: 'xpertz_add_gift_course',
						nonce: config.giftNonce || '',
						productId: giftForm.dataset.productId,
						email: data.get( 'email' ) || '',
						name: data.get( 'name' ) || '',
						message: data.get( 'message' ) || '',
					} ),
				} );
				const payload = await response.json();
				if ( payload.success === false || payload.error ) {
					throw new Error( payload.data?.message || config.strings?.error );
				}
				applyFragments( payload.fragments );
				giftForm.reset();
				closeGiftDialog();
				toast( config.strings?.giftAdded || 'Gift course added to cart' );
				window.setTimeout( () => doc.querySelector( '[data-popover-toggle="cart"]' )?.click(), 80 );
			} catch ( error ) {
				toast( error.message || config.strings?.error || 'Something went wrong.', true );
			} finally {
				submit.disabled = false;
				submit.removeAttribute( 'aria-busy' );
			}
		} );
	}

	const searchDialog = doc.querySelector( '[data-search-dialog]' );
	const searchInput = doc.querySelector( '[data-global-search]' );
	const searchResults = doc.querySelector( '[data-search-results]' );
	let searchTimer = 0;
	let searchRequest = null;
	let activeResult = -1;

	const openSearch = () => {
		if ( ! searchDialog ) {
			return;
		}
		closePopovers();
		previousFocus = doc.activeElement;
		searchDialog.hidden = false;
		activeLayer = searchDialog;
		setPageLock();
		window.setTimeout( () => searchInput && searchInput.focus(), 20 );
	};

	const closeSearch = () => {
		if ( ! searchDialog || searchDialog.hidden ) {
			return;
		}
		searchDialog.hidden = true;
		activeLayer = null;
		setPageLock();
		if ( previousFocus && typeof previousFocus.focus === 'function' ) {
			previousFocus.focus();
		}
	};

	const renderSearchResults = ( results ) => {
		if ( ! searchResults ) {
			return;
		}
		searchResults.replaceChildren();
		activeResult = -1;
		if ( ! results.length ) {
			const empty = doc.createElement( 'p' );
			empty.className = 'xhc-empty';
			empty.textContent = config.strings?.noResults || 'No results found.';
			searchResults.append( empty );
			return;
		}
		results.forEach( ( result ) => {
			const link = doc.createElement( 'a' );
			link.className = 'xhc-search-result';
			link.href = result.url;
			if ( result.image ) {
				const image = doc.createElement( 'img' );
				image.src = result.image;
				image.alt = '';
				image.loading = 'lazy';
				link.append( image );
			} else {
				const placeholder = doc.createElement( 'span' );
				placeholder.className = 'xhc-search-placeholder';
				link.append( placeholder );
			}
			const copy = doc.createElement( 'span' );
			const title = doc.createElement( 'strong' );
			const subtitle = doc.createElement( 'small' );
			title.textContent = result.title;
			subtitle.textContent = result.subtitle;
			copy.append( title, subtitle );
			link.append( copy );
			searchResults.append( link );
		} );
	};

	const runSearch = ( query ) => {
		if ( ! searchResults || query.trim().length < 2 ) {
			return;
		}
		if ( searchRequest ) {
			searchRequest.abort();
		}
		searchRequest = new AbortController();
		searchResults.replaceChildren();
		const loading = doc.createElement( 'p' );
		loading.className = 'xhc-empty';
		loading.textContent = config.strings?.searching || 'Searching…';
		searchResults.append( loading );
		const url = new URL( config.ajaxUrl, window.location.origin );
		url.searchParams.set( 'action', 'xpertz_global_search' );
		url.searchParams.set( 'nonce', config.searchNonce || '' );
		url.searchParams.set( 'query', query.trim() );
		window.fetch( url, { credentials: 'same-origin', signal: searchRequest.signal } )
			.then( ( response ) => response.json() )
			.then( ( payload ) => renderSearchResults( payload.success ? payload.data.results || [] : [] ) )
			.catch( ( error ) => {
				if ( error.name !== 'AbortError' ) {
					renderSearchResults( [] );
				}
			} );
	};

	doc.addEventListener( 'click', ( event ) => {
		if ( event.target.closest( '[data-search-open]' ) ) {
			event.preventDefault();
			closeMobile();
			openSearch();
		}
		if ( event.target.closest( '[data-search-close]' ) ) {
			event.preventDefault();
			closeSearch();
		}
		const suggestion = event.target.closest( '.xhc-search-suggestions button' );
		if ( suggestion && searchInput ) {
			searchInput.value = suggestion.textContent.trim();
			runSearch( searchInput.value );
		}
	} );

	if ( searchInput ) {
		searchInput.addEventListener( 'input', () => {
			window.clearTimeout( searchTimer );
			searchTimer = window.setTimeout( () => runSearch( searchInput.value ), 230 );
		} );
		searchInput.addEventListener( 'keydown', ( event ) => {
			const results = Array.from( doc.querySelectorAll( '.xhc-search-result' ) );
			if ( ! results.length || ! [ 'ArrowDown', 'ArrowUp', 'Enter' ].includes( event.key ) ) {
				return;
			}
			event.preventDefault();
			if ( event.key === 'Enter' && activeResult >= 0 ) {
				window.location.assign( results[ activeResult ].href );
				return;
			}
			activeResult = event.key === 'ArrowDown'
				? ( activeResult + 1 ) % results.length
				: ( activeResult - 1 + results.length ) % results.length;
			results.forEach( ( result, index ) => result.classList.toggle( 'is-active', index === activeResult ) );
		} );
	}

	const drawer = doc.querySelector( '.xhc-mobile-drawer' );
	const overlay = doc.querySelector( '.xhc-mobile-overlay' );
	const mobileToggle = doc.querySelector( '[data-mobile-open]' );

	function openMobile() {
		if ( ! drawer || ! overlay ) {
			return;
		}
		closePopovers();
		previousFocus = doc.activeElement;
		overlay.hidden = false;
		drawer.classList.add( 'is-open' );
		drawer.setAttribute( 'aria-hidden', 'false' );
		mobileToggle?.setAttribute( 'aria-expanded', 'true' );
		activeLayer = drawer;
		setPageLock();
		window.setTimeout( () => drawer.querySelector( '[data-mobile-close]' )?.focus(), 20 );
	}

	function closeMobile() {
		if ( ! drawer || ! drawer.classList.contains( 'is-open' ) ) {
			return;
		}
		drawer.classList.remove( 'is-open' );
		drawer.setAttribute( 'aria-hidden', 'true' );
		mobileToggle?.setAttribute( 'aria-expanded', 'false' );
		if ( overlay ) {
			overlay.hidden = true;
		}
		activeLayer = null;
		setPageLock();
		previousFocus?.focus?.();
	}

	doc.addEventListener( 'click', ( event ) => {
		if ( event.target.closest( '[data-mobile-open]' ) ) {
			event.preventDefault();
			openMobile();
		}
		if ( event.target.closest( '[data-mobile-close]' ) ) {
			event.preventDefault();
			closeMobile();
		}
		const submenuButton = event.target.closest( '.xhc-mobile-nav .xhc-has-submenu > button, .xhc-mobile-nav .xhc-submenu-toggle' );
		if ( submenuButton ) {
			event.preventDefault();
			const item = submenuButton.closest( 'li' );
			const open = ! item.classList.contains( 'is-open' );
			item.classList.toggle( 'is-open', open );
			submenuButton.setAttribute( 'aria-expanded', String( open ) );
		}
	} );

	if ( drawer ) {
		drawer.querySelectorAll( '.menu-item-has-children' ).forEach( ( item ) => {
			const link = item.querySelector( ':scope > a' );
			const submenu = item.querySelector( ':scope > .sub-menu' );
			if ( ! link || ! submenu ) {
				return;
			}
			const button = doc.createElement( 'button' );
			button.type = 'button';
			button.className = 'xhc-submenu-toggle';
			button.setAttribute( 'aria-expanded', 'false' );
			button.setAttribute( 'aria-label', `Toggle ${ link.textContent.trim() } submenu` );
			button.textContent = '+';
			item.insertBefore( button, submenu );
		} );
	}

	doc.addEventListener( 'keydown', ( event ) => {
		if ( event.key === 'Escape' ) {
			closePopovers();
			closeSearch();
			closeMobile();
			closeGiftDialog();
			return;
		}
		if ( event.key !== 'Tab' || ! activeLayer ) {
			return;
		}
		const items = focusable( activeLayer );
		if ( ! items.length ) {
			return;
		}
		const first = items[ 0 ];
		const last = items[ items.length - 1 ];
		if ( event.shiftKey && doc.activeElement === first ) {
			event.preventDefault();
			last.focus();
		} else if ( ! event.shiftKey && doc.activeElement === last ) {
			event.preventDefault();
			first.focus();
		}
	} );

	const addToCartUrl = () => {
		if ( window.wc_add_to_cart_params?.wc_ajax_url ) {
			return window.wc_add_to_cart_params.wc_ajax_url.replace( '%%endpoint%%', 'add_to_cart' );
		}
		const url = new URL( window.location.href );
		url.searchParams.set( 'wc-ajax', 'add_to_cart' );
		return url.toString();
	};

	const applyFragments = ( fragments ) => {
		Object.entries( fragments || {} ).forEach( ( [ selector, html ] ) => {
			doc.querySelectorAll( selector ).forEach( ( target ) => {
				const template = doc.createElement( 'template' );
				template.innerHTML = html.trim();
				const replacement = template.content.firstElementChild;
				if ( replacement ) {
					target.replaceWith( replacement );
				}
			} );
		} );
		if ( window.jQuery ) {
			window.jQuery( doc.body ).trigger( 'wc_fragments_refreshed' );
		}
	};

	const addCourseToCart = async ( button, buyNow = false ) => {
		const productId = button.dataset.addCourseToCart || button.dataset.buyCourseNow;
		if ( ! productId || button.disabled ) {
			return;
		}
		button.disabled = true;
		button.setAttribute( 'aria-busy', 'true' );
		const originalText = button.textContent;
		try {
			const response = await window.fetch( addToCartUrl(), {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body: new URLSearchParams( { product_id: productId, quantity: '1' } ),
			} );
			const payload = await response.json();
			if ( payload.error ) {
				if ( payload.product_url ) {
					window.location.assign( payload.product_url );
					return;
				}
				throw new Error( config.strings?.error || 'Unable to add this course.' );
			}
			applyFragments( payload.fragments );
			if ( buyNow ) {
				window.location.assign( config.checkoutUrl );
				return;
			}
			toast( config.strings?.added || 'Added to cart' );
			window.setTimeout( () => doc.querySelector( '[data-popover-toggle="cart"]' )?.click(), 80 );
		} catch ( error ) {
			toast( error.message || config.strings?.error || 'Something went wrong.', true );
		} finally {
			button.disabled = false;
			button.removeAttribute( 'aria-busy' );
			if ( originalText ) {
				button.textContent = originalText;
			}
		}
	};

	doc.addEventListener( 'click', ( event ) => {
		const addButton = event.target.closest( '[data-add-course-to-cart], [data-buy-course-now]' );
		if ( addButton ) {
			event.preventDefault();
			addCourseToCart( addButton, Boolean( addButton.dataset.buyCourseNow ) );
		}
	} );

	const setWishlistState = ( courseId, saved, count ) => {
		doc.querySelectorAll( `[data-wishlist-course="${ escapeSelector( courseId ) }"]` ).forEach( ( button ) => {
			button.classList.toggle( 'is-saved', saved );
			button.setAttribute( 'aria-pressed', String( saved ) );
			const label = button.querySelector( 'span' );
			if ( label && button.closest( '.xpc-secondary-actions' ) ) {
				label.textContent = saved ? config.strings?.savedLabel || 'Saved' : config.strings?.wishlist || 'Wishlist';
			}
		} );
		doc.querySelectorAll( '[data-wishlist-count]' ).forEach( ( badge ) => {
			badge.textContent = String( count );
			badge.classList.toggle( 'is-empty', ! count );
		} );
	};

	doc.addEventListener( 'click', async ( event ) => {
		const button = event.target.closest( '[data-wishlist-course]' );
		if ( ! button || button.dataset.xhcPending === 'true' ) {
			return;
		}
		event.preventDefault();
		event.stopImmediatePropagation();
		button.dataset.xhcPending = 'true';
		button.setAttribute( 'aria-busy', 'true' );
		try {
			const response = await window.fetch( config.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: new URLSearchParams( {
					action: 'xpertz_toggle_wishlist',
					nonce: config.wishlistNonce || '',
					courseId: button.dataset.wishlistCourse,
				} ),
			} );
			const payload = await response.json();
			if ( ! payload.success ) {
				throw new Error( payload.data?.message || config.strings?.error );
			}
			setWishlistState( button.dataset.wishlistCourse, payload.data.saved, payload.data.count );
			toast( payload.data.saved ? config.strings?.saved : config.strings?.removed );
			if ( button.closest( '.xhc-account-course-card' ) && ! payload.data.saved && window.location.pathname.includes( 'wishlist' ) ) {
				button.closest( '.xhc-account-course-card' ).remove();
			}
		} catch ( error ) {
			toast( error.message || config.strings?.error || 'Something went wrong.', true );
		} finally {
			delete button.dataset.xhcPending;
			button.removeAttribute( 'aria-busy' );
		}
	}, true );

	doc.addEventListener( 'click', async ( event ) => {
		const button = event.target.closest( '[data-share-wishlist]' );
		if ( ! button ) {
			return;
		}
		event.preventDefault();
		const courseIds = Array.from( doc.querySelectorAll( '.xhc-account-course-card[data-course-id]' ) )
			.map( ( card ) => card.dataset.courseId )
			.filter( Boolean );
		const shareUrl = new URL( config.wishlistUrl || window.location.href, window.location.origin );
		if ( courseIds.length ) {
			shareUrl.searchParams.set( 'xpertz_courses', courseIds.join( ',' ) );
		}
		try {
			if ( navigator.share ) {
				await navigator.share( { title: doc.title, url: shareUrl.toString() } );
			} else {
				await navigator.clipboard.writeText( shareUrl.toString() );
				toast( config.strings?.shareDone || 'Wishlist link copied' );
			}
		} catch ( error ) {
			if ( error.name !== 'AbortError' ) {
				toast( config.strings?.error || 'Something went wrong.', true );
			}
		}
	} );

	const setScrolledState = () => doc.body.classList.toggle( 'xhc-scrolled', window.scrollY > 24 );
	setScrolledState();
	window.addEventListener( 'scroll', setScrolledState, { passive: true } );

	if ( new URLSearchParams( window.location.search ).get( 'action' ) === 'register' ) {
		window.setTimeout( () => doc.querySelector( '.woocommerce-form-register input:not([type="hidden"])' )?.focus(), 150 );
	}
} )();
