<?php
namespace Helper\Acceptance;

/**
 * Helper methods and actions related to the ConvertKit Plugin's Broadcasts
 * functionality, which are then available using $I->{yourFunctionName}.
 *
 * @since   1.9.7.5
 */
class ConvertKitBroadcasts extends \Codeception\Module
{
	/**
	 * Helper method to setup the Plugin's Broadcasts to Posts settings.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I          AcceptanceTester.
	 * @param   bool|array       $settings   Array of key/value settings. If not defined, uses expected defaults.
	 */
	public function setupConvertKitPluginBroadcasts($I, $settings = false)
	{
		// Go to the Plugin's Broadcasts screen.
		$I->loadConvertKitSettingsBroadcastsScreen($I);

		// Complete fields.
		if ( $settings ) {
			foreach ( $settings as $key => $value ) {
				switch ( $key ) {
					case 'enabled':
					case 'import_thumbnail':
					case 'enabled_export':
					case 'no_styles':
						if ( $value ) {
							$I->checkOption('_wp_convertkit_settings_broadcasts[' . $key . ']');
						} else {
							$I->uncheckOption('_wp_convertkit_settings_broadcasts[' . $key . ']');
						}
						break;

					case 'post_status':
					case 'category_id':
					case 'author_id':
						$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_broadcasts_' . $key . '-container', $value);

						break;
					default:
						$I->fillField('_wp_convertkit_settings_broadcasts[' . $key . ']', $value);
						break;
				}
			}
		}

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to load the Plugin's Settings > Broadcasts screen.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function loadConvertKitSettingsBroadcastsScreen($I)
	{
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=broadcasts');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Check that expected HTML exists in the DOM of the page we're viewing for
	 * a Broadcasts block or shortcode, based on its configuration.
	 *
	 * @since   1.9.7.5
	 *
	 * @param   AcceptanceTester $I                      Tester.
	 * @param   bool|int         $numberOfPosts          Number of Broadcasts listed.
	 * @param   bool|string      $seePrevPaginationLabel Test if the "previous" pagination link is output and matches expected label.
	 * @param   bool|string      $seeNextPaginationLabel Test if the "next" pagination link is output and matches expected label.
	 * @param   bool             $seeGrid                Test if the broadcasts are displayed in grid view (false = list view).
	 * @param   bool             $seeImage               Test if the broadcasts display images.
	 * @param   bool             $seeDescription         Test if the broadcasts display descriptions.
	 * @param   bool|string      $seeReadMore            Test if the broadcasts display a read more link matching the given text.
	 */
	public function seeBroadcastsOutput($I, $numberOfPosts = false, $seePrevPaginationLabel = false, $seeNextPaginationLabel = false, $seeGrid = false, $seeImage = false, $seeDescription = false, $seeReadMore = false)
	{
		// Confirm that the block displays.
		$I->seeElementInDOM('div.convertkit-broadcasts');
		$I->seeElementInDOM('div.convertkit-broadcasts ul.convertkit-broadcasts-list');
		$I->seeElementInDOM('div.convertkit-broadcasts ul.convertkit-broadcasts-list li.convertkit-broadcast');
		$I->seeElementInDOM('div.convertkit-broadcasts ul.convertkit-broadcasts-list li.convertkit-broadcast a.convertkit-broadcast-title');

		// Confirm that UTM parameters exist on a broadcast link.
		$I->assertStringContainsString(
			'utm_source=wordpress&utm_term=en_US&utm_content=convertkit',
			$I->grabAttributeFrom('a.convertkit-broadcast-title', 'href')
		);

		// If Display as grid is enabled, confirm the applicable HTML exists so that CSS can style this layout.
		if ($seeGrid) {
			$I->seeElementInDOM('div.convertkit-broadcasts[data-display-grid="1"]');
		} else {
			$I->dontSeeElementInDOM('div.convertkit-broadcasts[data-display-grid="1"]');
		}

		// If Display image is enabled, confirm the image is displayed.
		if ($seeImage) {
			$I->seeElementInDOM('a.convertkit-broadcast-image img');
			$I->assertStringContainsString(
				'utm_source=wordpress&utm_term=en_US&utm_content=convertkit',
				$I->grabAttributeFrom('a.convertkit-broadcast-image', 'href')
			);
		} else {
			$I->dontSeeElementInDOM('a.convertkit-broadcast-image img');
		}

		// If Display description is enabled, confirm the description is displayed.
		if ($seeDescription) {
			$I->seeElementInDOM('.convertkit-broadcast-description');
		} else {
			$I->dontSeeElementInDOM('.convertkit-broadcast-description');
		}

		// If Display read more link is enabled, confirm the read more link is displayed and matches the given text.
		if ($seeReadMore) {
			$I->seeElementInDOM('a.convertkit-broadcast-read-more');
			$I->assertStringContainsString(
				'utm_source=wordpress&utm_term=en_US&utm_content=convertkit',
				$I->grabAttributeFrom('a.convertkit-broadcast-read-more', 'href')
			);
		} else {
			$I->dontSeeElementInDOM('a.convertkit-broadcast-read-more');
		}

		// Confirm that the number of expected broadcasts displays.
		if ($numberOfPosts !== false) {
			$I->seeNumberOfElements('li.convertkit-broadcast', $numberOfPosts);
		}

		// Confirm that previous pagination displays, if expected.
		if ($seePrevPaginationLabel !== false) {
			$I->seeElementInDOM('div.convertkit-broadcasts ul.convertkit-broadcasts-pagination li.convertkit-broadcasts-pagination-prev a');
			$I->seeInSource($seePrevPaginationLabel);
		}

		// Confirm that next pagination displays, if expected.
		if ($seeNextPaginationLabel !== false) {
			$I->seeElementInDOM('div.convertkit-broadcasts ul.convertkit-broadcasts-pagination li.convertkit-broadcasts-pagination-next a');
		}
	}

	/**
	 * Tests that the Broadcasts pagination works, and that the expected Broadcast
	 * is displayed after using previous and next links.
	 *
	 * @since   2.0.0
	 *
	 * @param   AcceptanceTester $I                      Tester.
	 * @param   string           $previousLabel          Previous / Newer Broadcasts Label.
	 * @param   string           $nextLabel              Next / Older Broadcasts Label.
	 */
	public function testBroadcastsPagination($I, $previousLabel, $nextLabel)
	{
		// Confirm that the block displays one broadcast with a pagination link to older broadcasts.
		$I->seeBroadcastsOutput($I, 1, false, $nextLabel);

		// Click the Older Posts link.
		$I->click('li.convertkit-broadcasts-pagination-next a');

		// Wait for the AJAX request to complete, by checking if the convertkit-broadcasts-loading class has been
		// removed from the block.
		$I->waitForBroadcastsToLoad($I);

		// Confirm that the block displays one broadcast with a pagination link to newer broadcasts.
		$I->seeBroadcastsOutput($I, 1, $previousLabel, false);

		// Confirm that the expected Broadcast name is displayed and links to the expected URL, with UTM parameters.
		$I->seeInSource('<a href="' . $_ENV['CONVERTKIT_API_BROADCAST_SECOND_URL'] . '?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit" target="_blank" rel="nofollow noopener"');
		$I->seeInSource($_ENV['CONVERTKIT_API_BROADCAST_SECOND_TITLE']);

		// Click the Newer Posts link.
		$I->click('li.convertkit-broadcasts-pagination-prev a');

		// Wait for the AJAX request to complete, by checking if the convertkit-broadcasts-loading class has been
		// removed from the block.
		$I->waitForBroadcastsToLoad($I);

		// Confirm that the block displays one broadcast with a pagination link to older broadcasts.
		$I->seeBroadcastsOutput($I, 1, false, $nextLabel);

		// Confirm that the expected Broadcast name is displayed and links to the expected URL, with UTM parameters.
		$I->seeInSource('<a href="' . $_ENV['CONVERTKIT_API_BROADCAST_FIRST_URL'] . '?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit" target="_blank" rel="nofollow noopener"');
		$I->seeInSource($_ENV['CONVERTKIT_API_BROADCAST_FIRST_TITLE']);
	}

	/**
	 * Wait for the AJAX request to complete, by checking if the convertkit-broadcasts-loading class has been
	 * removed from the block.
	 *
	 * @since   1.9.7.6
	 *
	 * @param   AcceptanceTester $I                      Tester.
	 */
	public function waitForBroadcastsToLoad($I)
	{
		$I->waitForElementChange(
			'div.convertkit-broadcasts',
			function(\Facebook\WebDriver\WebDriverElement $el) {
				return ( strpos($el->getAttribute('class'), 'convertkit-broadcasts-loading') === false ? true : false );
			},
			5
		);
	}
}
