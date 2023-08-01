<?php
/**
 * Tests Broadcasts to Posts import functionality.
 *
 * @since   2.2.8
 */
class BroadcastsToPostsCest
{
	/**
	 * The WordPress Cron event name to test.
	 *
	 * @since   2.2.8
	 *
	 * @var     string
	 */
	private $cronEventName = 'convertkit_resource_refresh_broadcasts';

	/**
	 * The WordPress Category name, used for tests that assign imported Broadcasts
	 * to Posts where the Category setting is defined.
	 * 
	 * @since 	2.2.8
	 * 
	 * @var 	string
	 */
	private $categoryName = 'ConvertKit Broadcasts to Posts';

	/**
	 * The WordPress Category created before each test was run.
	 * 
	 * @since 	2.2.8
	 * 
	 * @var 	int
	 */
	private $categoryID = 0;

	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate ConvertKit Plugin.
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Activate WP Crontrol, to manually run scheduled events.
		$I->activateThirdPartyPlugin($I, 'wp-crontrol');

		// Create a Category named 'ConvertKit Broadcasts to Posts'.
		$this->categoryID = $I->haveTermInDatabase($this->categoryName, 'category');
	}

	/**
	 * Tests that Broadcasts import when enabled in the Plugin's settings.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsImportWhenEnabled(AcceptanceTester $I)
	{
		// Enable Broadcasts to Posts.
		$I->setupConvertKitPluginBroadcastsToPosts(
			$I,
			[
				'enabled'          => true,
				'send_at_min_date' => '01/01/2020',
			]
		);

		// Run the WordPress Cron event to import Broadcasts to WordPress Posts.
		$I->runCronEvent($I, $this->cronEventName);

		// Load the Posts screen.
		$I->amOnAdminPage('edit.php');

		// Check that no PHP warnings or notices were output.
		//$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm expected Broadcasts exist as Posts.
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_FIRST_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_SECOND_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_THIRD_TITLE']);
	}

	/**
	 * Tests that Broadcasts import when enabled in the Plugin's settings
	 * a Category is defined and the Category is assigned to the created
	 * WordPress Posts.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsImportWithCategoryEnabled(AcceptanceTester $I)
	{
		// Enable Broadcasts to Posts.
		$I->setupConvertKitPluginBroadcastsToPosts(
			$I,
			[
				'enabled'          => true,
				'category'		   => $this->categoryName,
				'send_at_min_date' => '01/01/2020',
			]
		);

		// Run the WordPress Cron event to import Broadcasts to WordPress Posts.
		$I->runCronEvent($I, $this->cronEventName);

		// Load the Posts screen.
		$I->amOnAdminPage('edit.php');

		// Check that no PHP warnings or notices were output.
		//$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm expected Broadcasts exist as Posts.
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_FIRST_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_SECOND_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_THIRD_TITLE']);

		// Confirm each Post is assigned to the Category.
		// @TODO.
	}

	/**
	 * Tests that Broadcasts do not import when enabled in the Plugin's settings
	 * and an Earliest Date is specified that is newer than any Broadcasts sent
	 * on the ConvertKit account.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsImportWithEarliestDate(AcceptanceTester $I)
	{
		// Enable Broadcasts to Posts.
		$I->setupConvertKitPluginBroadcastsToPosts(
			$I,
			[
				'enabled'          => true,
				'send_at_min_date' => '01/07/2023',
			]
		);

		// Run the WordPress Cron event to import Broadcasts to WordPress Posts.
		$I->runCronEvent($I, $this->cronEventName);

		// Load the Posts screen.
		$I->amOnAdminPage('edit.php');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm no Broadcasts exist as Posts.
		$I->dontSee($_ENV['CONVERTKIT_API_BROADCAST_FIRST_TITLE']);
		$I->dontSee($_ENV['CONVERTKIT_API_BROADCAST_SECOND_TITLE']);
		$I->dontSee($_ENV['CONVERTKIT_API_BROADCAST_THIRD_TITLE']);
	}

	/**
	 * Tests that Broadcasts import when enabled in the Plugin's settings
	 * a Member Content option is defined and the Member Content option is
	 * assigned to the created WordPress Posts.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsImportWithMemberContentEnabled(AcceptanceTester $I)
	{
		// Enable Broadcasts to Posts.
		$I->setupConvertKitPluginBroadcastsToPosts(
			$I,
			[
				'enabled'          => true,
				'send_at_min_date' => '01/01/2020',
				'restrict_content' => $_ENV['CONVERTKIT_API_PRODUCT_NAME'],
			]
		);

		// Run the WordPress Cron event to import Broadcasts to WordPress Posts.
		$I->runCronEvent($I, $this->cronEventName);

		// Load the Posts screen.
		$I->amOnAdminPage('edit.php');

		// Check that no PHP warnings or notices were output.
		//$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm expected Broadcasts exist as Posts.
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_FIRST_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_SECOND_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_THIRD_TITLE']);

		// Confirm each Post's Restrict Content setting is correct.
		// @TODO.

		// Test the first Post's Restrict Content functionality.
		// @TODO.
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->deactivateThirdPartyPlugin($I, 'wp-crontrol');
		$I->resetConvertKitPlugin($I);

		// Remove Category named 'ConvertKit Broadcasts to Posts'.
		$I->dontHaveTermInDatabase(
			array(
				'name' => 'ConvertKit Broadcasts to Posts',
			)
		);

		// Remove imported Posts.
		$I->dontHavePostInDatabase([
			'post_type' => 'post',
			'post_status' => 'publish', 
		], true);
	}
}
