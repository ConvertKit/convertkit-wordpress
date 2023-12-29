<?php
/**
 * Tests Restrict Content by Product functionality on WordPress Pages.
 *
 * @since   2.1.0
 */
class RestrictContentProductPageCest
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
		// Activate ConvertKit plugin.
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test that content is not restricted when not configured on a WordPress Page.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentWhenDisabled(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin, disabling JS.
		$I->setupConvertKitPluginDisableJS($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Restrict Content: Product');

		// Add blocks.
		$I->addGutenbergParagraphBlock($I, 'Visible content.');
		$I->addGutenbergBlock($I, 'More', 'more');
		$I->addGutenbergParagraphBlock($I, 'Member only content.');

		// Publish Page.
		$url = $I->publishGutenbergPage($I);

		// Confirm that all content is displayed.
		$I->amOnUrl($url);
		$I->see('Visible content.');
		$I->see('Member only content.');
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
		// Setup ConvertKit Plugin, disabling JS.
		$I->setupConvertKitPluginDisableJS($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Restrict Content: Product');

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

		// Publish Page.
		$url = $I->publishGutenbergPage($I);

		// Test Restrict Content functionality.
		$I->testRestrictedContentByProductOnFrontend(
			$I,
			$url,
			'Visible content.',
			'Member only content.'
		);
	}

	/**
	 * Test that restricting content by a Product specified in the Page Settings works when
	 * creating and viewing a new WordPress Page, and that the WordPress generated Page Excerpt
	 * is displayed when no more tag exists.
	 *
	 * @since   2.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentByProductWithGeneratedExcerpt(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin, disabling JS.
		$I->setupConvertKitPluginDisableJS($I);

		// Define visible content and member only content.
		$visibleContent    = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec at velit purus. Nam gravida tempor tellus, sit amet euismod arcu. Mauris sed mattis leo. Mauris viverra eget tellus sit amet vehicula. Nulla eget sapien quis felis euismod pellentesque. Quisque elementum et diam nec eleifend. Sed ornare quam eget augue consequat, in maximus quam fringilla. Morbi';
		$memberOnlyContent = 'Member only content';

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Restrict Content: Product: Generated Excerpt');

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
		$I->addGutenbergParagraphBlock($I, $visibleContent);
		$I->addGutenbergParagraphBlock($I, $memberOnlyContent);

		// Publish Page.
		$url = $I->publishGutenbergPage($I);

		// Test Restrict Content functionality.
		$I->testRestrictedContentByProductOnFrontend(
			$I,
			$url,
			$visibleContent,
			$memberOnlyContent
		);
	}

	/**
	 * Test that restricting content by a Product specified in the Page Settings works when
	 * creating and viewing a new WordPress Page, and JS is enabled to allow the modal
	 * version for the authentication flow to be used.
	 *
	 * @since   2.3.8
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentModalByProduct(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Restrict Content: Product: Modal');

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

		// Publish Page.
		$url = $I->publishGutenbergPage($I);

		// Test Restrict Content functionality.
		$I->testRestrictedContentModalByProductOnFrontend(
			$I,
			$url,
			'Visible content.',
			'Member only content.'
		);
	}

	/**
	 * Test that restricting content by a Product that does not exist does not output
	 * a fatal error and instead displays all of the Page's content.
	 *
	 * This checks for when a Product is deleted in ConvertKit, but is still specified
	 * as the Restrict Content setting for a Page.
	 *
	 * @since   2.3.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentByInvalidProduct(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin, disabling JS.
		$I->setupConvertKitPluginDisableJS($I);

		// Programmatically create a Page.
		$pageID = $I->createRestrictedContentPage(
			$I,
			[
				'post_title' => 'ConvertKit: Page: Restrict Content: Invalid Product',
				'restrict_content_setting' => 'product_12345' // A fake Product that does not exist in ConvertKit.
			]
		);

		// Navigate to the page.
		$I->amOnPage('?p=' . $pageID);

		// Confirm all content displays, with no errors, as the Product is invalid.
		$I->testRestrictContentDisplaysContent($I);
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
		// Setup ConvertKit Plugin, disabling JS.
		$I->setupConvertKitPluginDisableJS($I);

		// Programmatically create a Page.
		$pageID = $I->createRestrictedContentPage(
			$I,
			[
				'post_title' => 'ConvertKit: Page: Restrict Content: Product: ' . $_ENV['CONVERTKIT_API_PRODUCT_NAME'] . ': Quick Edit',
			]
		);

		// Quick Edit the Page in the Pages WP_List_Table.
		$I->quickEdit(
			$I,
			'page',
			$pageID,
			[
				'restrict_content' => [ 'select', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
			]
		);

		// Test Restrict Content functionality.
		$I->testRestrictedContentByProductOnFrontend($I, $pageID);
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
		// Setup ConvertKit Plugin, disabling JS.
		$I->setupConvertKitPluginDisableJS($I);

		// Programmatically create two Pages.
		$pageIDs = array(
			$I->createRestrictedContentPage(
				$I,
				[
					'post_title' => 'ConvertKit: Page: Restrict Content: Product: ' . $_ENV['CONVERTKIT_API_PRODUCT_NAME'] . ': Bulk Edit #1',
				]
			),
			$I->createRestrictedContentPage(
				$I,
				[
					'post_title' => 'ConvertKit: Page: Restrict Content: Product: ' . $_ENV['CONVERTKIT_API_PRODUCT_NAME'] . ': Bulk Edit #2',
				]
			),
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

		// Iterate through Pages to run frontend tests.
		foreach ($pageIDs as $pageID) {
			// Test Restrict Content functionality.
			$I->testRestrictedContentByProductOnFrontend($I, $pageID);
			$I->resetCookie('ck_subscriber_id');
		}
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
