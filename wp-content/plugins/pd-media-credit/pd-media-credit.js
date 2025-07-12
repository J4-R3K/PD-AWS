jQuery( function ( $ ) {
	if ( ! PD_MC.cap ) { return; }

	/* save on button click */
	$( '.wp-list-table' ).on( 'click', '.pd-mc-save', function () {
		const $btn = $( this );
		const $row = $btn.closest( 'tr' );
		const $inp = $row.find( '.pd-mc-credit' );
		send( $btn.data( 'attach' ), $inp.val(), $inp, $btn );
	} );

	/* or save when user presses Enter in the input */
	$( '.wp-list-table' ).on( 'keypress', '.pd-mc-credit', function ( e ) {
		if ( e.which !== 13 ) { return; }
		e.preventDefault();
		const $inp = $( this );
		const $btn = $inp.siblings( '.pd-mc-save' );
		send( $inp.data( 'attach' ), $inp.val(), $inp, $btn );
	} );

	function send ( attachID, credit, $inp, $btn ) {
		$btn.prop( 'disabled', true ).text( '…' );

		$.post( PD_MC.ajax, {
			action : 'pd_mc_update_credit',
			attach : attachID,
			credit : credit,
			nonce  : $inp.siblings( 'input[name^="pd_mc_credit_"]' ).val()
		}, function ( resp ) {
			flash( $inp, resp.success ? '#d4edda' : '#f8d7da' );   // green / red
			$btn.prop( 'disabled', false ).text( 'Save' );
			if ( ! resp.success ) {
				console.warn( 'Credit save failed →', resp );
			}
		} );
	}

	function flash ( $el, color ) {
		const orig = $el.css( 'background-color' );
		$el.css( 'background', color );
		setTimeout( () => $el.css( 'background', orig ), 900 );
	}
} );

