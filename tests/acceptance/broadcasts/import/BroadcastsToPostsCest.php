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
	private $cronEventName = 'convertkit_resource_refresh_posts';

	/**
	 * The WordPress Category name, used for tests that assign imported Broadcasts
	 * to Posts where the Category setting is defined.
	 *
	 * @since   2.2.8
	 *
	 * @var     string
	 */
	private $categoryName = 'ConvertKit Broadcasts to Posts';

	/**
	 * The WordPress Category created before each test was run.
	 *
	 * @since   2.2.8
	 *
	 * @var     int
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
		$result           = $I->haveTermInDatabase($this->categoryName, 'category');
		$this->categoryID = $result[0]; // term_id.
	}

	/**
	 * Tests that Broadcasts do not import when disabled in the Plugin's settings.
	 *
	 * @since   2.2.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsImportWhenDisabled(AcceptanceTester $I)
	{
		// Enable Broadcasts to Posts.
		$I->setupConvertKitPluginBroadcasts(
			$I,
			[
				'enabled' => false,
			]
		);

		// Run the WordPress Cron event to refresh Broadcasts.
		$I->runCronEvent($I, $this->cronEventName);

		// Wait a few seconds for the Cron event to complete importing Broadcasts.
		$I->wait(7);

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
	 * Tests that Broadcasts import when enabled in the Plugin's settings.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsImportWhenEnabled(AcceptanceTester $I)
	{
		// Enable Broadcasts to Posts.
		$I->setupConvertKitPluginBroadcasts(
			$I,
			[
				'enabled'               => true,
				'published_at_min_date' => '01/01/2020',
			]
		);

		// Run the WordPress Cron event to import Broadcasts to WordPress Posts.
		$I->runCronEvent($I, $this->cronEventName);

		// Wait a few seconds for the Cron event to complete importing Broadcasts.
		$I->wait(7);

		// Load the Posts screen.
		$I->amOnAdminPage('edit.php');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm expected Broadcasts exist as Posts.
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_FIRST_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_SECOND_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_THIRD_TITLE']);

		// Get created Post IDs.
		$postIDs = [
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(1)', 'id')),
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(2)', 'id')),
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(3)', 'id')),
		];

		// View the first post.
		$I->amOnPage('?p=' . $postIDs[0]);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Set cookie with signed subscriber ID, as if we completed the Restrict Content authentication flow.
		$I->setCookie('ck_subscriber_id', $_ENV['CONVERTKIT_API_SIGNED_SUBSCRIBER_ID']);

		// Reload the post.
		$I->reloadPage();

		// Confirm inline styles exist in the imported Broadcast.
		$I->seeElementInDOM('div.ck-inner-section');
		$I->assertNotNull($I->grabAttributeFrom('div.wp-block-post-content h1', 'style'));

		// Confirm tracking image has been removed.
		$I->dontSee('<img src="https://preview.convertkit-mail2.com/open" alt="">');

		// Confirm unsubscribe link section has been removed.
		$I->dontSee('<div class="ck-section ck-hide-in-public-posts"');

		// Confirm published date matches the Broadcast.
		$date = date('Y-m-d', strtotime($_ENV['CONVERTKIT_API_BROADCAST_FIRST_DATE'])) . 'T' . date('H:i:s', strtotime($_ENV['CONVERTKIT_API_BROADCAST_FIRST_DATE']));
		$I->seeInSource('<time datetime="' . $date);
	}

	/**
	 * Tests that Broadcasts import when enabled and then 'Import now' button
	 * is used.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsManualImportWhenEnabled(AcceptanceTester $I)
	{
		// Enable Broadcasts to Posts.
		$I->setupConvertKitPluginBroadcasts(
			$I,
			[
				'enabled'               => true,
				'published_at_min_date' => '01/01/2020',
			]
		);

		// Click the Import now button.
		$I->click('Import now');

		// Confirm a success message displays.
		$I->see('Broadcasts import started. Check the Posts screen shortly to confirm Broadcasts imported successfully.');

		// Wait a few seconds for the Cron event to complete importing Broadcasts.
		$I->wait(7);

		// Load the Posts screen.
		$I->amOnAdminPage('edit.php');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm expected Broadcasts exist as Posts.
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_FIRST_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_SECOND_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_THIRD_TITLE']);

		// Get created Post IDs.
		$postIDs = [
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(1)', 'id')),
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(2)', 'id')),
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(3)', 'id')),
		];

		// View the first post.
		$I->amOnPage('?p=' . $postIDs[0]);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Set cookie with signed subscriber ID, as if we completed the Restrict Content authentication flow.
		$I->setCookie('ck_subscriber_id', $_ENV['CONVERTKIT_API_SIGNED_SUBSCRIBER_ID']);

		// Reload the post.
		$I->reloadPage();

		// Confirm inline styles exist in the imported Broadcast.
		$I->seeElementInDOM('div.ck-inner-section');
		$I->assertNotNull($I->grabAttributeFrom('div.wp-block-post-content h1', 'style'));

		// Confirm published date matches the Broadcast.
		$date = date('Y-m-d', strtotime($_ENV['CONVERTKIT_API_BROADCAST_FIRST_DATE'])) . 'T' . date('H:i:s', strtotime($_ENV['CONVERTKIT_API_BROADCAST_FIRST_DATE']));
		$I->seeInSource('<time datetime="' . $date);
	}

	/**
	 * Tests that Broadcasts import when enabled in the Plugin's settings,
	 * a Post Status is defined and the Post Status is assigned to the created
	 * WordPress Posts.
	 *
	 * @since   2.3.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsImportWithPostStatusEnabled(AcceptanceTester $I)
	{
		// Enable Broadcasts to Posts.
		$I->setupConvertKitPluginBroadcasts(
			$I,
			[
				'enabled'               => true,
				'post_status'           => 'private',
				'category_id'           => $this->categoryName,
				'published_at_min_date' => '01/01/2020',
			]
		);

		// Run the WordPress Cron event to import Broadcasts to WordPress Posts.
		$I->runCronEvent($I, $this->cronEventName);

		// Wait a few seconds for the Cron event to complete importing Broadcasts.
		$I->wait(7);

		// Load the Posts screen.
		$I->amOnAdminPage('edit.php');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm expected Broadcasts exist as Posts.
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_FIRST_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_SECOND_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_THIRD_TITLE']);

		// Get created Post IDs.
		$postIDs = [
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(1)', 'id')),
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(2)', 'id')),
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(3)', 'id')),
		];

		// Confirm each Post's status is private.
		foreach ($postIDs as $postID) {
			$I->seePostInDatabase(
				[
					'ID'          => $postID,
					'post_status' => 'private',
				]
			);
		}
	}

	/**
	 * Tests that Broadcasts import when enabled in the Plugin's settings,
	 * an Author is defined and the Author is assigned to the created
	 * WordPress Posts.
	 *
	 * @since   2.3.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsImportWithAuthorIDEnabled(AcceptanceTester $I)
	{
		// Add a WordPress User with an Editor role.
		$I->haveUserInDatabase( 'editor', 'editor' );

		// Enable Broadcasts to Posts.
		$I->setupConvertKitPluginBroadcasts(
			$I,
			[
				'enabled'               => true,
				'author_id'             => 'editor',
				'category_id'           => $this->categoryName,
				'published_at_min_date' => '01/01/2020',
			]
		);

		// Run the WordPress Cron event to import Broadcasts to WordPress Posts.
		$I->runCronEvent($I, $this->cronEventName);

		// Wait a few seconds for the Cron event to complete importing Broadcasts.
		$I->wait(7);

		// Load the Posts screen.
		$I->amOnAdminPage('edit.php');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm expected Broadcasts exist as Posts.
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_FIRST_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_SECOND_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_THIRD_TITLE']);

		// Get created Post IDs.
		$postIDs = [
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(1)', 'id')),
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(2)', 'id')),
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(3)', 'id')),
		];

		// Confirm each Post's status is private.
		foreach ($postIDs as $postID) {
			$I->seePostInDatabase(
				[
					'ID'          => $postID,
					'post_author' => '2',
				]
			);
		}
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
		$I->setupConvertKitPluginBroadcasts(
			$I,
			[
				'enabled'               => true,
				'category_id'           => $this->categoryName,
				'published_at_min_date' => '01/01/2020',
			]
		);

		// Run the WordPress Cron event to import Broadcasts to WordPress Posts.
		$I->runCronEvent($I, $this->cronEventName);

		// Wait a few seconds for the Cron event to complete importing Broadcasts.
		$I->wait(7);

		// Load the Posts screen.
		$I->amOnAdminPage('edit.php');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm expected Broadcasts exist as Posts.
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_FIRST_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_SECOND_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_THIRD_TITLE']);

		// Get created Post IDs.
		$postIDs = [
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(1)', 'id')),
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(2)', 'id')),
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(3)', 'id')),
		];

		// Confirm each Post is assigned to the Category.
		foreach ($postIDs as $postID) {
			// Confirm the Post is published.
			$I->seePostInDatabase(
				[
					'ID'          => $postID,
					'post_status' => 'publish',
				]
			);

			// Confirm the Post is assigned to the Category.
			$I->seePostWithTermInDatabase($postID, $this->categoryID, null, 'category');
		}
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
		$I->setupConvertKitPluginBroadcasts(
			$I,
			[
				'enabled'               => true,
				'published_at_min_date' => '01/01/2030',
			]
		);

		// Run the WordPress Cron event to import Broadcasts to WordPress Posts.
		$I->runCronEvent($I, $this->cronEventName);

		// Wait a few seconds for the Cron event to complete.
		$I->wait(7);

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
		$I->setupConvertKitPluginBroadcasts(
			$I,
			[
				'enabled'               => true,
				'published_at_min_date' => '01/01/2020',
			]
		);

		// Run the WordPress Cron event to import Broadcasts to WordPress Posts.
		$I->runCronEvent($I, $this->cronEventName);

		// Wait a few seconds for the Cron event to complete importing Broadcasts.
		$I->wait(7);

		// Load the Posts screen.
		$I->amOnAdminPage('edit.php');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm expected Broadcasts exist as Posts.
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_FIRST_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_SECOND_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_THIRD_TITLE']);

		// Confirm the HTML Template Test's Restrict Content setting is correct.
		$I->click($_ENV['CONVERTKIT_API_BROADCAST_FIRST_TITLE']);

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed.
		$I->maybeCloseGutenbergWelcomeModal($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm Restrict Content setting is correct.
		$I->seeInField('wp-convertkit[restrict_content]', $_ENV['CONVERTKIT_API_PRODUCT_NAME']);
	}

	/**
	 * Tests that Broadcasts import when enabled in the Plugin's settings,
	 * with the Disable Styles setting enabled.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsImportWithDisableStylesEnabled(AcceptanceTester $I)
	{
		// Enable Broadcasts to Posts.
		$I->setupConvertKitPluginBroadcasts(
			$I,
			[
				'enabled'               => true,
				'published_at_min_date' => '01/01/2020',
				'no_styles'             => true,
			]
		);

		// Run the WordPress Cron event to import Broadcasts to WordPress Posts.
		$I->runCronEvent($I, $this->cronEventName);

		// Wait a few seconds for the Cron event to complete importing Broadcasts.
		$I->wait(7);

		// Load the Posts screen.
		$I->amOnAdminPage('edit.php');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm expected Broadcasts exist as Posts.
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_FIRST_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_SECOND_TITLE']);
		$I->see($_ENV['CONVERTKIT_API_BROADCAST_THIRD_TITLE']);

		// Get created Post IDs.
		$postIDs = [
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(1)', 'id')),
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(2)', 'id')),
			(int) str_replace('post-', '', $I->grabAttributeFrom('tbody#the-list > tr:nth-child(3)', 'id')),
		];

		// View the first post.
		$I->amOnPage('?p=' . $postIDs[0]);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Set cookie with signed subscriber ID, as if we completed the Restrict Content authentication flow.
		$I->setCookie('ck_subscriber_id', $_ENV['CONVERTKIT_API_SIGNED_SUBSCRIBER_ID']);

		// Reload the post.
		$I->reloadPage();

		// Confirm no inline styles exist in the imported Broadcast.
		$I->dontSeeElementInDOM('div.ck-inner-section');
		$I->assertNull($I->grabAttributeFrom('div.wp-block-post-content h1', 'style'));
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
		$I->dontHavePostInDatabase(
			[
				'post_type' => 'post',
			],
			true
		);
	}
}
