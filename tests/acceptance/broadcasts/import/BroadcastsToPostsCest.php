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

			// Edit Post.
			$I->amOnAdminPage('post.php?post=' . $postID . '&action=edit');
			$I->maybeCloseGutenbergWelcomeModal($I);
			$I->click('Featured image');
			$I->wait(2);
			$I->see('XXX');

			// Confirm Featured Image exists.
			$I->seePostMetaInDatabase(
				[
					'post_id'  => $postID,
					'meta_key' => '_thumbnail_id',
				]
			);
		}
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
