(function( window, $, undefined ) { "use strict";

	var settings      = window._wpmejsSettings || {};
	settings.features = settings.features || mejs.MepDefaults.features;
	settings.features.push( "castilo" );
	$.extend( mejs.MepDefaults, {
		hideVolumeOnTouchDevices: true,
		audioVolume: "vertical",
	} );
	MediaElementPlayer.prototype.buildcastilo = function( player, controls, layers, media ) {
		player.container.addClass( "castilo-mejs-container" );
		var player_parent = player.container.parent( ".podcast-episode-player" );
		if ( player_parent.length > 0 ) {
			// manually add duration to the player (as it is prefered to not preload any data, so we can properly collect statistics only when the user clicks the play button)
			var episode_duration = player_parent.data( "episode-duration" );
			if ( episode_duration ) {
				controls.find( "span.mejs-duration" ).html( episode_duration );
			}
			// add download button
			var download_file = player_parent.data( "episode-download" );
			if ( download_file ) {
				var download_button = $( '<div class="mejs-button mejs-download-button"><a href="' + download_file + '" title="' + player_parent.data( "episode-download-button" ) + '"><span class="screen-reader-text">' + player_parent.data( "episode-download-button" ) + '</span></a></div>' );
				download_button.appendTo( controls );
			}
			// add transcript button
			var transcript_file = player_parent.data( "episode-transcript" );
			if ( transcript_file ) {
				var transcript_button = $( '<div class="mejs-button mejs-transcript-button"><a href="' + transcript_file + '" title="' + player_parent.data( "episode-transcript-button" ) + '" target="_blank"><span class="screen-reader-text">' + player_parent.data( "episode-transcript-button" ) + '</span></a></div>' );
				transcript_button.appendTo( controls );
			}
		}
	};

	// Handle audio timeline jumping points for episodes.
	$( '.jump-point[href^="#"]' ).on( 'click.castilo', function( e ) {
		if ( $( '.podcast-episode-player .castilo-mejs-container' ) ) {
			e.preventDefault();

			var jumping_point = $( this ).attr( 'href' ).substr( 1 ), player = $( '.podcast-episode-player .castilo-mejs-container audio' ).get( 0 ).player, jumping_point_seconds = 0, m = 1, p = jumping_point.split( ':' );
			while ( p.length > 0 ) {
				jumping_point_seconds += m * parseInt( p.pop(), 10 );
				m                     *= 60;
			}
			if ( jumping_point_seconds > 0 ) {
				if ( true == player.paused ) {
					player.play();
				}
				player.setCurrentTime( jumping_point_seconds );
			}
		}
	});

})( this, jQuery );
