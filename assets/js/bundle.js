/**
 * Herbal Pearls — Bundle Add-to-Cart
 * Vanilla rewrite (no jQuery). Mirrors v2 Appendix A.1 behavior.
 *
 * @package HerbalPearls
 */

(function () {
	'use strict';

	if ( typeof window === 'undefined' || typeof document === 'undefined' ) {
		return;
	}

	document.addEventListener( 'submit', function ( e ) {
		var form = e.target;
		if ( ! form || form.id !== 'hp-bundle-form' ) {
			return;
		}
		e.preventDefault();

		var btn = form.querySelector( 'button[type=submit]' );
		var msg = form.querySelector( '.hp-bundle-msg' );
		var originalBtnText = btn ? btn.textContent : '';

		var data = new FormData();
		data.append( 'action', 'hp_add_bundle_to_cart' );
		data.append( 'nonce', window.HP_BUNDLE && window.HP_BUNDLE.nonce ? window.HP_BUNDLE.nonce : '' );
		data.append( 'bundle_id', form.dataset.bundleId || '' );

		form.querySelectorAll( 'select[name^=selections]' ).forEach( function ( sel ) {
			var match = sel.name.match( /\[(\d+)\]/ );
			if ( match ) {
				data.append( 'selections[' + match[ 1 ] + ']', sel.value );
			}
		} );

		if ( btn ) {
			btn.disabled = true;
			btn.textContent = 'Adding…';
		}
		if ( msg ) {
			msg.classList.remove( 'error', 'success' );
			msg.textContent = '';
		}

		var endpoint = ( window.HP_BUNDLE && window.HP_BUNDLE.ajaxurl ) || '/wp-admin/admin-ajax.php';

		fetch( endpoint, {
			method: 'POST',
			body: data,
			credentials: 'same-origin',
		} )
			.then( function ( res ) {
				return res.json().catch( function () { return null; } ).then( function ( body ) {
					return { ok: res.ok, body: body };
				} );
			} )
			.then( function ( result ) {
				var body = result.body;
				if ( result.ok && body && body.success ) {
					if ( msg ) {
						msg.classList.add( 'success' );
						msg.textContent = 'Added to cart!';
					}
					if ( body.data && body.data.cart_url ) {
						window.location.href = body.data.cart_url;
					}
				} else {
					var errMsg = ( body && body.data && body.data.message ) || 'Could not add bundle.';
					if ( msg ) {
						msg.classList.add( 'error' );
						msg.textContent = errMsg;
					}
					if ( btn ) {
						btn.disabled = false;
						btn.textContent = originalBtnText || 'Add bundle to cart';
					}
				}
			} )
			.catch( function () {
				if ( msg ) {
					msg.classList.add( 'error' );
					msg.textContent = 'Network error.';
				}
				if ( btn ) {
					btn.disabled = false;
					btn.textContent = originalBtnText || 'Add bundle to cart';
				}
			} );
	}, false );
})();
