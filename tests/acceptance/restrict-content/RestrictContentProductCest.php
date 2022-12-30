<?php
/**
 * Tests Restrict Content by Product functionality.
 *
 * @since   2.1.0
 */
class RestrictContentProductCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate and Setup ConvertKit plugin.
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);
	}

	/**
	 * Test that restricting content by a Product specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentByProduct(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Restrict Content: Product');

		// Configure metabox's Restrict Content setting = Tag name.
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

		// Publish Page.
		$url = $I->publishGutenbergPage($I);
	}

	/**
	 * Test that restricting content by a Product specified in the Page Settings works when
	 * using the Quick Edit functionality.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentByProductUsingQuickEdit(AcceptanceTester $I)
	{
		// Programmatically create a Page.
		$pageID = $I->createRestrictedContentPage($I, 'ConvertKit: Page: Restrict Content: Product: ' . $_ENV['CONVERTKIT_API_PRODUCT_NAME'] . ': Quick Edit');

		// Quick Edit the Page in the Pages WP_List_Table.
		$I->quickEdit(
			$I,
			'page',
			$pageID,
			[
				'restrict_content' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
			]
		);
	}

	/**
	 * Test that restricting content by a Product specified in the Page Settings works when
	 * using the Bulk Edit functionality.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentByProductUsingBulkEdit(AcceptanceTester $I)
	{
		// Programmatically create two Pages.
		$pageIDs = array(
			$I->createRestrictedContentPage($I, 'ConvertKit: Page: Restrict Content: Product: ' . $_ENV['CONVERTKIT_API_PRODUCT_NAME'] . ': Bulk Edit #1'),
			$I->createRestrictedContentPage($I, 'ConvertKit: Page: Restrict Content: Product: ' . $_ENV['CONVERTKIT_API_PRODUCT_NAME'] . ': Bulk Edit #2'),
		);

		// Bulk Edit the Pages in the Pages WP_List_Table.
		$I->bulkEdit(
			$I,
			'page',
			$pageIDs,
			[
				'restrict_content' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
			]
		);
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.1.0
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
