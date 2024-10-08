<?php
/**
 * Tests Restrict Content by Tag functionality.
 *
 * @since   2.3.2
 */
class RestrictContentTagCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.3.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate and Setup ConvertKit plugin.
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
	}

	/**
	 * Test that restricting content by a Tag specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 *
	 * @since   2.3.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentByTag(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'Kit: Page: Restrict Content: Tag');

		// Configure metabox's Restrict Content setting = Tag name.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form'             => [ 'select2', 'None' ],
				'restrict_content' => [ 'select2', $_ENV['CONVERTKIT_API_TAG_NAME'] ],
			]
		);

		// Add blocks.
		$I->addGutenbergParagraphBlock($I, 'Visible content.');
		$I->addGutenbergBlock($I, 'More', 'more');
		$I->addGutenbergParagraphBlock($I, 'Member-only content.');

		// Publish Page.
		$url = $I->publishGutenbergPage($I);

		// Test Restrict Content functionality.
		$I->testRestrictedContentByTagOnFrontend($I, $url, $I->generateEmailAddress());
	}

	/**
	 * Test that restricting content by a Tag that does not exist does not output
	 * a fatal error and instead displays all of the Page's content.
	 *
	 * This checks for when a Tag is deleted in ConvertKit, but is still specified
	 * as the Restrict Content setting for a Page.
	 *
	 * @since   2.3.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentByInvalidTag(AcceptanceTester $I)
	{
		// Programmatically create a Page.
		$pageID = $I->createRestrictedContentPage(
			$I,
			[
				'post_title'               => 'Kit: Page: Restrict Content: Invalid Tag',
				'restrict_content_setting' => 'tag_12345', // A fake Tag that does not exist in ConvertKit.
			]
		);

		// Navigate to the page.
		$I->amOnPage('?p=' . $pageID);

		// Confirm all content displays, with no errors, as the Tag is invalid.
		$I->testRestrictContentDisplaysContent($I);
	}

	/**
	 * Test that restricting content by a Tag specified in the Page Settings works when
	 * using the Quick Edit functionality.
	 *
	 * @since   2.3.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentByTagUsingQuickEdit(AcceptanceTester $I)
	{
		// Programmatically create a Page.
		$pageID = $I->createRestrictedContentPage(
			$I,
			[
				'post_title' => 'Kit: Page: Restrict Content: Tag: ' . $_ENV['CONVERTKIT_API_TAG_NAME'] . ': Quick Edit',
			]
		);

		// Quick Edit the Page in the Pages WP_List_Table.
		$I->quickEdit(
			$I,
			'page',
			$pageID,
			[
				'restrict_content' => [ 'select', $_ENV['CONVERTKIT_API_TAG_NAME'] ],
			]
		);

		// Test Restrict Content functionality.
		$I->testRestrictedContentByTagOnFrontend($I, $pageID, $I->generateEmailAddress());
	}

	/**
	 * Test that restricting content by a Tag specified in the Page Settings works when
	 * using the Bulk Edit functionality.
	 *
	 * @since   2.3.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentByTagUsingBulkEdit(AcceptanceTester $I)
	{
		// Programmatically create two Pages.
		$pageIDs = array(
			$I->createRestrictedContentPage(
				$I,
				[
					'post_title' => 'Kit: Page: Restrict Content: Tag: ' . $_ENV['CONVERTKIT_API_TAG_NAME'] . ': Bulk Edit #1',
				]
			),
			$I->createRestrictedContentPage(
				$I,
				[
					'post_title' => 'Kit: Page: Restrict Content: Tag: ' . $_ENV['CONVERTKIT_API_TAG_NAME'] . ': Bulk Edit #2',
				]
			),
		);

		// Bulk Edit the Pages in the Pages WP_List_Table.
		$I->bulkEdit(
			$I,
			'page',
			$pageIDs,
			[
				'restrict_content' => [ 'select', $_ENV['CONVERTKIT_API_TAG_NAME'] ],
			]
		);

		// Iterate through Pages to run frontend tests.
		foreach ($pageIDs as $pageID) {
			// Test Restrict Content functionality.
			$I->testRestrictedContentByTagOnFrontend($I, $pageID, $I->generateEmailAddress());
		}
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.3.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->resetCookie('ck_subscriber_id');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
