<?php
/**
 * Tests Post export to Broadcast functionality.
 *
 * @since   2.4.0
 */
class BroadcastsExportPostRowActionCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.4.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate ConvertKit Plugin.
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);
	}

	/**
	 * Tests that no action is displayed in the Posts table when the 'Enable Export Actions' is disabled
	 * in the Plugin's settings.
	 *
	 * @since   2.4.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsExportRowActionWhenDisabled(AcceptanceTester $I)
	{
		// Programmatically create a Post.
		$postID = $I->havePostInDatabase(
			[
				'post_type'    => 'post',
				'post_title'   => 'ConvertKit: Export Post to Broadcast',
				'post_content' => 'ConvertKit: Export Post to Broadcast: Content',
				'post_excerpt' => 'ConvertKit: Export Post to Broadcast: Excerpt',
			]
		);

		// Navigate to the Posts WP_List_Table.
		$I->amOnAdminPage('edit.php');

		// Hover mouse over Post's table row.
		$I->moveMouseOver('tr.iedit:first-child');

		// Confirm that no action to export the Post is displayed.

	}

	/**
	 * Tests that an action is displayed in the Posts table when the 'Enable Export Actions' is enabled
	 * in the Plugin's settings, and a Broadcast is created in ConvertKit when clicked.
	 *
	 * @since   2.4.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsExportRowActionWhenEnabled(AcceptanceTester $I)
	{
		// Enable Export Actions for Posts.
		$I->setupConvertKitPluginBroadcasts(
			$I,
			[
				'enabled_export' => true,
			]
		);

		// Programmatically create a Post.
		$postID = $I->havePostInDatabase(
			[
				'post_type'    => 'post',
				'post_title'   => 'ConvertKit: Export Post to Broadcast',
				'post_content' => 'ConvertKit: Export Post to Broadcast: Content',
				'post_excerpt' => 'ConvertKit: Export Post to Broadcast: Excerpt',
			]
		);

		// Navigate to the Posts WP_List_Table.
		$I->amOnAdminPage('edit.php');

		// Hover mouse over Post's table row.
		$I->moveMouseOver('tr.iedit:first-child');

		// Click the export action.

		// Confirm that a success message displays.

		// Delete Broadcast.
		
	}

	/**
	 * Tests that the 'Disable Styles' setting is honored when enabled in the Plugin's settings, and a
	 * Broadcast is created in ConvertKit.
	 *
	 * @since   2.4.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsExportActionWithDisableStylesEnabled(AcceptanceTester $I)
	{

	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.4.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
