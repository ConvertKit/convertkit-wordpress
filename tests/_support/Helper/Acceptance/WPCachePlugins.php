<?php
namespace Helper\Acceptance;

/**
 * Helper methods and actions related to WordPress Caching Plugins,
 * which are then available using $I->{yourFunctionName}.
 *
 * @since   2.2.2
 */
class WPCachePlugins extends \Codeception\Module
{
	/**
	 * Helper method to activate the LiteSpeed Plugin, and then
	 * enable it to perform caching.
	 *
	 * @since   2.2.2
	 *
	 * @param   AcceptanceTester $I      Acceptance Tester.
	 */
	public function activeAndEnableLiteSpeedCachePlugin($I)
	{
		// Clear up any cache configuration files that might exist from previous tests.
		$I->deleteAdvancedCacheConfig($I);

		// Activate Plugin.
		$I->activateThirdPartyPlugin($I, 'litespeed-cache');

		// Navigate to its settings screen.
		$I->amOnAdminPage('admin.php?page=litespeed-cache');

		// Enable.
		$I->click('label[for="input_radio_cache_1"]');

		// Save.
		$I->click('Save Changes');
	}

	/**
	 * Helper method to activate the W3 Total Cache Plugin, and then
	 * enable it to perform caching.
	 *
	 * @since   2.2.2
	 *
	 * @param   AcceptanceTester $I      Acceptance Tester.
	 */
	public function activeAndEnableW3TotalCachePlugin($I)
	{
		// Clear up any cache configuration files that might exist from previous tests.
		$I->deleteAdvancedCacheConfig($I);

		// Activate Plugin.
		$I->activateThirdPartyPlugin($I, 'w3-total-cache');

		// Navigate to its settings screen.
		$I->amOnAdminPage('admin.php?page=w3tc_general');

		// Skip Setup Guide.
		$I->click('#w3tc-wizard-skip');

		// Navigate to its settings screen.
		$I->waitForElementVisible('input.w3tc-gopro-button');
		$I->amOnAdminPage('admin.php?page=w3tc_general');

		// Enable.
		$I->checkOption('#pgcache__enabled');

		// Save.
		$I->click('Save all settings');
	}

	/**
	 * Helper method to enable caching in the WP Fastest Cache Plugin.
	 *
	 * @since   2.2.2
	 *
	 * @param   AcceptanceTester $I      Acceptance Tester.
	 */
	public function enableCachingWPFastestCachePlugin($I)
	{
		// Navigate to its settings screen.
		$I->amOnAdminPage('admin.php?page=wpfastestcacheoptions');

		// Enable.
		$I->checkOption('input[name="wpFastestCacheStatus"]');

		// Save.
		$I->click('Submit');

		// The ConvertKit Plugin will now automatically add an exclusion rule
		// to WP Fastest Cache. Check this rule does exist in the settings.
		$I->amOnAdminPage('admin.php?page=wpfastestcacheoptions');
		$I->click('label[for="wpfc-exclude"]');
		$I->see('Contains: ck_subscriber_id');
	}

	/**
	 * Helper method to enable caching in the WP Optimize Plugin.
	 *
	 * @since   2.2.2
	 *
	 * @param   AcceptanceTester $I      Acceptance Tester.
	 */
	public function enableCachingWPOptimizePlugin($I)
	{
		// Navigate to its settings screen.
		$I->amOnAdminPage('admin.php?page=wpo_cache');

		// Dismiss notice.
		$I->click('Dismiss');

		// Enable.
		$I->click('div.cache-options label.switch');

		// Save.
		$I->waitForElementVisible('#wp-optimize-purge-cache');
		$I->click('#wp-optimize-save-cache-settings');
	}

	/**
	 * Helper method to enable caching in the WP Super Cache Plugin.
	 *
	 * @since   2.2.2
	 *
	 * @param   AcceptanceTester $I      Acceptance Tester.
	 */
	public function enableCachingWPSuperCachePlugin($I)
	{
		// Navigate to its settings screen.
		$I->amOnAdminPage('options-general.php?page=wpsupercache');

		// Enable.
		$I->selectOption('input[name="wp_cache_easy_on"]', '1');

		// Save.
		$I->click('Update Status');
	}

	/**
	 * Helper method to exclude caching when a cookie is present
	 * in the WP Super Cache Plugin.
	 *
	 * @since   2.2.2
	 *
	 * @param   AcceptanceTester $I      Acceptance Tester.
	 */
	public function excludeCachingWPSuperCachePlugin($I, $cookieName = 'ck_subscriber_id')
	{
		// Navigate to its settings screen.
		$I->amOnAdminPage('options-general.php?page=wpsupercache&tab=settings');

		// Add cookis to "Rejected Cookies" setting.
		$I->fillField('wp_rejected_cookies', $cookieName);

		// Save.
		$I->click('form[name="wp_edit_rejected_cookies"] input.button-primary');
	}

	/**
	 * Helper method to delete the files at wp-content/advanced-cache.php
	 * and wp-content/wp-cache-config.php, which may have been created by a
	 * previous caching plugin that was enabled in a previous test.
	 * 
	 * @since 	2.2.2
	 *
	 * @param   AcceptanceTester $I      Acceptance Tester.
	 */
	public function deleteWPCacheConfigFiles($I)
	{
		if (file_exists($_ENV['WP_ROOT_FOLDER'] . '/wp-content/advanced-cache.php')) {
			$I->deleteFile($_ENV['WP_ROOT_FOLDER'] . '/wp-content/advanced-cache.php');
		}
		if (file_exists($_ENV['WP_ROOT_FOLDER'] . '/wp-content/wp-cache-config.php')) {
			$I->deleteFile($_ENV['WP_ROOT_FOLDER'] . '/wp-content/wp-cache-config.php');
		}
	}
}
