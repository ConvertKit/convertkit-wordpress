<?php
namespace Helper\Acceptance;

/**
 * Helper methods and actions related to the WP Super Cache Plugin,
 * which are then available using $I->{yourFunctionName}.
 *
 * @since   2.2.0
 */
class WPSuperCachePlugin extends \Codeception\Module
{
	/**
	 * Helper method to activate the WP Super Cache Plugin, and then
	 * enable it to perform caching.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I      Acceptance Tester.
	 */
	public function activeAndEnableWPSuperCachePlugin($I)
	{
		// Activate Plugin.
		$I->activateThirdPartyPlugin($I, 'wp-super-cache');

		// Navigate to its settings screen.
		$I->amOnAdminPage('options-general.php?page=wpsupercache');

		// Enable.
		$I->selectOption('input[name="wp_cache_easy_on"]', '1');

		// Save.
		$I->click('Update Status');
	}
}
