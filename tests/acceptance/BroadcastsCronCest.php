<?php
/**
 * Tests for the Broadcasts WordPress Cron event.
 * 
 * @since 	1.9.7.4
 */
class BroadcastsCronCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate WP Crontrol Plugin.
		$I->activateThirdPartyPlugin($I, 'wp-crontrol');
	}

	/**
	 * Tests that the Broadcasts Cron event is created when activating the Plugin.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testCronEventCreatedOnActivation(AcceptanceTester $I)
	{
		// Activate the Plugin.
		$I->activateConvertKitPlugin($I);

		// Check that the cron event registered and is set to run hourly.
		$I->seeCronEvent($I, 'convertkit_refresh_convertkit_posts', 'convertkit_refresh_convertkit_posts()', 'Once Hourly');
	}

	/**
	 * Tests that the Broadcasts Cron event is created when simlulating an upgrade from < 1.9.7.4 to 1.9.7.4
	 * or higher.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testCronEventCreatedOnUpgrade(AcceptanceTester $I)
	{
		// Activate the Plugin.
		$I->activateConvertKitPlugin($I);

		// Delete the Cron event.
		$I->deleteCronEvent($I, 'convertkit_refresh_convertkit_posts');

		// Check that the cron event unregistered and no longer exists.
		$I->dontSeeCronEvent($I, 'convertkit_refresh_convertkit_posts', 'convertkit_refresh_convertkit_posts()', 'Once Hourly');

		// Downgrade the Plugin's version number in the option table, to simulate that we're on e.g. 1.9.7.3 or older.
		$I->haveOptionInDatabase('convertkit_version', '1.9.7.2');

		// Navigate to an administration screen, which will trigger the Plugin's update routine to register the event
		// and update the Plugin's version number in the options table.
		$I->amOnAdminPage('index.php');

		// Check that the cron event registered and is set to run hourly.
		$I->seeCronEvent($I, 'convertkit_refresh_convertkit_posts', 'convertkit_refresh_convertkit_posts()', 'Once Hourly');
	}

	/**
	 * Tests that the Broadcasts Cron event is destroyed when deactivating the Plugin.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testCronEventDestroyedOnDeactivation(AcceptanceTester $I)
	{
		// Activate the Plugin.
		$I->activateConvertKitPlugin($I);

		// Deactivate the Plugin.
		$I->deactivateConvertKitPlugin($I);

		// Check that the cron event unregistered and no longer exists.
		$I->dontSeeCronEvent($I, 'convertkit_refresh_convertkit_posts', 'convertkit_refresh_convertkit_posts()', 'Once Hourly');
	}

	/**
	 * Tests that the Broadcasts Cron event runs without errors when using valid API
	 * credentials.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testCronEventWithValidAPICredentials(AcceptanceTester $I)
	{
		// Activate and setup the Plugin.
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);

		// Run the cron event, as if WordPress' Cron would run it.

		// Observe the debug log file.


	}

	/**
	 * Tests that the Broadcasts Cron event runs without errors when using invalid API
	 * credentials, logging an error to the debug log.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testCronEventWithInvalidAPICredentials(AcceptanceTester $I)
	{
		// Activate and setup the Plugin.
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);

		// Run the cron event, as if WordPress' Cron would run it.

		// Observe the debug log file.
		

	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateThirdPartyPlugin($I, 'wp-crontrol');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}