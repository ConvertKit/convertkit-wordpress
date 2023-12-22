/**
 * Provides functionality for tabbed interfaces.
 *
 * @since   2.2.5
 *
 * @package ConvertKit
 * @author ConvertKit
 */

/**
 * Initializes tabbed interfaces
 *
 * @since 	2.2.5
 */
function convertKitTabsInit() {

	( function ( $ ) {

		// Safely call this function by destroying any previously initialized instances.
		convertKitTabsDestroy();

		// Iterate through all JS tab instances, initializing each one.
		$( '.convertkit-js-tabs' ).each(
			function () {

				const nav_tab_container  = $( this ),
				nav_tab_panels_container = $( nav_tab_container ).data( 'panels-container' ),
				nav_tab_panel            = $( nav_tab_container ).data( 'panel' ),
				nav_tab_active           = $( nav_tab_container ).data( 'active' ),
				match_height             = $( nav_tab_container ).data( 'match-height' );

				// Call update.
				convertKitTabsUpdate( nav_tab_container, nav_tab_panels_container, nav_tab_panel, nav_tab_active, $( 'a.' + nav_tab_active, $( nav_tab_container ) ).attr( 'href' ) );

				// If fix height is set, define the height of the content areas to match the parent of the nav tab container.
				if ( typeof match_height !== 'undefined' ) {
					$( nav_tab_panels_container ).height( $( match_height ).innerHeight() );
				}

				// Register a listener when a tab is clicked.
				$( nav_tab_container ).on(
					'click.convertkit_tabs',
					'a',
					function ( e ) {

						e.preventDefault();

						// Update the UI to show the active tab and content associated with it.
						convertKitTabsUpdate( nav_tab_container, nav_tab_panels_container, nav_tab_panel, nav_tab_active, $( this ).attr( 'href' ) );

					}
				);

			}
		);

	} )( jQuery );

}

/**
 * For the given active tab:
 * - Hides all other content in the group
 * - Shows content associated with the active tab
 *
 * @since 	1.0.0
 *
 * @param 	object 	nav_tab_container 			<ul> Navigation Tab Container
 * @param 	string 	nav_tab_panels_container 	ID of element containing content panel, to display, which associate with the navigation tabs
 * @param 	string 	nav_tab_panel 				Class of elements containing content panels, to hide, which associate with the navigation tabs
 * @param 	string 	nav_tab_active 				Class to apply to the clicked active_tab element
 * @param 	string 	active_tab 					ID of <a> tab which has been selected / clicked
 */
function convertKitTabsUpdate( nav_tab_container, nav_tab_panels_container, nav_tab_panel, nav_tab_active, active_tab ) {

	( function ( $ ) {

		// If we don't have an active tab at this point, we don't have any tabs, so bail.
		if ( typeof active_tab === 'undefined' ) {
			return;
		}
		if ( active_tab.length === 0 ) {
			return;
		}

		// Deactivate all tabs in this container.
		$( 'a', $( nav_tab_container ) ).removeClass( nav_tab_active );

		// Hide all panels in this container.
		$( nav_tab_panel, $( nav_tab_panels_container ) ).hide();

		// Activate the clicked / selected tab in this container.
		$( 'a[href="' + active_tab + '"]', $( nav_tab_container ) ).addClass( nav_tab_active );

		// Show the active tab's panels in this container.
		$( active_tab ).show();

	} )( jQuery );

}

/**
 * Destroys previously initialized tabbed interfaces, no longer
 * listening to events.
 *
 * @since 	1.0.0
 */
function convertKitTabsDestroy() {

	( function ( $ ) {

		// Iterate through all JS tab instances, destroying each one.
		$( '.convertkit-js-tabs' ).each(
			function () {

				$( this ).off( 'click.convertkit_tabs', 'a' );

			}
		);

	} )( jQuery );

}
