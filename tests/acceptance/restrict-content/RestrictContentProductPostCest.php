<?php
/**
 * Tests Restrict Content by Product functionality on WordPress Posts.
 *
 * @since   2.3.2
 */
class RestrictContentProductPostCest
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
	 * Test that restricting content by a Product specified in the Post Settings works when
	 * creating and viewing a new WordPress Post.
	 *
	 * @since   2.3.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentWhenDisabled(AcceptanceTester $I)
	{
		// Add a Post using the Gutenberg editor.
		$I->addGutenbergPage($I, 'post', 'ConvertKit: Post: Restrict Content: Product');

		// Confirm no option is displayed to restrict content.
		$I->dontSeeElementInDOM('#wp-convertkit-restrict_content');

		// Add blocks.
		$I->addGutenbergParagraphBlock($I, 'Visible content.');
		$I->addGutenbergBlock($I, 'More', 'more');
		$I->addGutenbergParagraphBlock($I, 'Member only content.');

		// Publish Post.
		$url = $I->publishGutenbergPage($I);

		// Confirm that all content is displayed.
		$I->amOnUrl($url);
		$I->see('Visible content.');
		$I->see('Member only content.');
	}

	/**
	 * Test that restricting content by a Product specified in the Post Settings works when
	 * creating and viewing a new WordPress Post.
	 *
	 * @since   2.3.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentByProduct(AcceptanceTester $I)
	{
		// Enable Restricted Content.
		$I->setupConvertKitPluginRestrictContent(
			$I,
			[
				'enabled' => 'on',
			]
		);

		// Add a Post using the Gutenberg editor.
		$I->addGutenbergPage($I, 'post', 'ConvertKit: Post: Restrict Content: Product');

		// Configure metabox's Restrict Content setting = Product name.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form'             => [ 'select2', 'None' ],
				'restrict_content' => [ 'select2', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
			]
		);

		// Add blocks.
		$I->addGutenbergParagraphBlock($I, 'Visible content.');
		$I->addGutenbergBlock($I, 'More', 'more');
		$I->addGutenbergParagraphBlock($I, 'Member only content.');

		// Publish Post.
		$url = $I->publishGutenbergPage($I);

		// Test Restrict Content functionality.
		$I->testRestrictedContentOnFrontend(
			$I,
			$url,
			'Visible content.',
			'Member only content.'
		);
	}

	/**
	 * Test that restricting content by a Product specified in the Post Settings works when
	 * using the Quick Edit functionality.
	 *
	 * @since   2.3.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentByProductUsingQuickEdit(AcceptanceTester $I)
	{
		// Enable Restricted Content.
		$I->setupConvertKitPluginRestrictContent(
			$I,
			[
				'enabled' => 'on',
			]
		);

		// Programmatically create a Post.
		$postID = $I->createRestrictedContentPage(
			$I,
			'post',
			'ConvertKit: Post: Restrict Content: Product: ' . $_ENV['CONVERTKIT_API_PRODUCT_NAME'] . ': Quick Edit'
		);

		// Quick Edit the Post in the Posts WP_List_Table.
		$I->quickEdit(
			$I,
			'post',
			$postID,
			[
				'restrict_content' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
			]
		);

		// Test Restrict Content functionality.
		$I->testRestrictedContentOnFrontend($I, $postID);
	}

	/**
	 * Test that restricting content by a Product specified in the Post Settings works when
	 * using the Bulk Edit functionality.
	 *
	 * @since   2.3.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentByProductUsingBulkEdit(AcceptanceTester $I)
	{
		// Enable Restricted Content.
		$I->setupConvertKitPluginRestrictContent(
			$I,
			[
				'enabled' => 'on',
			]
		);

		// Programmatically create two Posts.
		$postIDs = array(
			$I->createRestrictedContentPage($I, 'post', 'ConvertKit: Post: Restrict Content: Product: ' . $_ENV['CONVERTKIT_API_PRODUCT_NAME'] . ': Bulk Edit #1'),
			$I->createRestrictedContentPage($I, 'post', 'ConvertKit: Post: Restrict Content: Product: ' . $_ENV['CONVERTKIT_API_PRODUCT_NAME'] . ': Bulk Edit #2'),
		);

		// Bulk Edit the Posts in the Posts WP_List_Table.
		$I->bulkEdit(
			$I,
			'post',
			$postIDs,
			[
				'restrict_content' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
			]
		);

		// Iterate through Posts to run frontend tests.
		foreach ($postIDs as $postID) {
			// Test Restrict Content functionality.
			$I->testRestrictedContentOnFrontend($I, $postID);
			$I->resetCookie('ck_subscriber_id');
		}
	}

	/**
	 * Test that no option to restrict content by a Product is displayed when disabled and using
	 * the Bulk and Quick Edit functionality.
	 *
	 * @since   2.3.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentBulkQuickEditWhenDisabled(AcceptanceTester $I)
	{
		// Programmatically create two Posts.
		$postIDs = array(
			$I->createRestrictedContentPage($I, 'post', 'ConvertKit: Post: Restrict Content: Disabled: Bulk Edit #1'),
			$I->createRestrictedContentPage($I, 'post', 'ConvertKit: Post: Restrict Content: Disabled: Bulk Edit #2'),
		);

		// Navigate to Posts > Edit.
		$I->amOnAdminPage('edit.php?post_type=post');

		// Open Quick Edit form for the Post.
		$I->openQuickEdit($I, 'post', $postIDs[0]);

		// Confirm no option exists to restrict content.
		$I->dontSeeElementInDOM('#convertkit-quick-edit #wp-convertkit-quick-edit-restrict_content');

		// Cancel Quick Edit.
		$I->click('Cancel');

		// Open Bulk Edit form for the Posts.
		$I->openBulkEdit($I, 'post', $postIDs);

		// Confirm no option exists to restrict content.
		$I->dontSeeElementInDOM('#convertkit-bulk-edit #wp-convertkit-bulk-edit-restrict_content');
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
