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
		// Activate ConvertKit plugin.
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test that content is not restricted when not configured on a WordPress Post.
	 *
	 * @since   2.3.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentWhenDisabled(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin, disabling JS.
		$I->setupConvertKitPluginDisableJS($I);

		// Add a Post using the Gutenberg editor.
		$I->addGutenbergPage($I, 'post', 'ConvertKit: Post: Restrict Content: Product');

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
		// Setup ConvertKit Plugin, disabling JS.
		$I->setupConvertKitPluginDisableJS($I);

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
		$I->testRestrictedContentByProductOnFrontend(
			$I,
			$url,
			'Visible content.',
			'Member only content.'
		);
	}

	/**
	 * Test that restricting content by a Product specified in the Post Settings works when
	 * creating and viewing a new WordPress Post, and that the WordPress generated Post Excerpt
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

		// Add a Post using the Gutenberg editor.
		$I->addGutenbergPage($I, 'post', 'ConvertKit: Post: Restrict Content: Product: Generated Excerpt');

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
	 * Test that restricting content by a Product specified in the Post Settings works when
	 * creating and viewing a new WordPress Post, and that the Excerpt defined in the Post
	 * is displayed when no more tag exists.
	 *
	 * @since   2.3.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentByProductWithDefinedExcerpt(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin, disabling JS.
		$I->setupConvertKitPluginDisableJS($I);

		// Define visible content and member only content.
		$excerpt           = 'This is a defined excerpt';
		$memberOnlyContent = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec at velit purus. Nam gravida tempor tellus, sit amet euismod arcu. Mauris sed mattis leo. Mauris viverra eget tellus sit amet vehicula. Nulla eget sapien quis felis euismod pellentesque. Quisque elementum et diam nec eleifend. Sed ornare quam eget augue consequat, in maximus quam fringilla. Morbi';

		// Add a Post using the Gutenberg editor.
		$I->addGutenbergPage($I, 'post', 'ConvertKit: Post: Restrict Content: Product: Defined Excerpt');

		// Configure metabox's Restrict Content setting = Product name.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form'             => [ 'select2', 'None' ],
				'restrict_content' => [ 'select2', $_ENV['CONVERTKIT_API_PRODUCT_NAME'] ],
			]
		);

		// Add member content only as a block.
		// Visible content will be defined in the excerpt.
		$I->addGutenbergParagraphBlock($I, $memberOnlyContent);

		// Define excerpt.
		$I->addGutenbergExcerpt($I, $excerpt);

		// Publish Post.
		$url = $I->publishGutenbergPage($I);

		// Navigate to the page.
		$I->amOnUrl($url);

		// Test Restrict Content functionality.
		// Check content is not displayed, and CTA displays with expected text.
		$I->testRestrictContentByProductHidesContentWithCTA($I, $excerpt, $memberOnlyContent);

		// Test that the restricted content displays when a valid signed subscriber ID is used,
		// as if we entered the code sent in the email.
		// Excerpt should not be displayed, as its an excerpt, and we now show the member content instead.
		$I->testRestrictedContentShowsContentWithValidSubscriberID($I, $url, '', $memberOnlyContent);

		// Assert that the excerpt is no longer displayed.
		$I->dontSee($excerpt);
	}

	/**
	 * Test that restricting content by a Product specified in the Post Settings works when
	 * creating and viewing a new WordPress Post, and JS is enabled to allow the modal
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

		// Add a Post using the Gutenberg editor.
		$I->addGutenbergPage($I, 'post', 'ConvertKit: Post: Restrict Content: Product: Modal');

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
		$I->testRestrictedContentModalByProductOnFrontend(
			$I,
			$url,
			'Visible content.',
			'Member only content.'
		);
	}

	/**
	 * Test that restricting content by a Product that does not exist does not output
	 * a fatal error and instead displays all of the Post's content.
	 *
	 * This checks for when a Product is deleted in ConvertKit, but is still specified
	 * as the Restrict Content setting for a Post.
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
		$postID = $I->createRestrictedContentPage(
			$I,
			'post',
			'ConvertKit: Post: Restrict Content: Invalid Product',
			'Visible content.',
			'Member only content.',
			'product_12345' // A fake Product that does not exist in ConvertKit.
		);

		// Navigate to the post.
		$I->amOnPage('?p=' . $postID);

		// Confirm all content displays, with no errors, as the Product is invalid.
		$I->testRestrictContentDisplaysContent($I);
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
		// Setup ConvertKit Plugin, disabling JS.
		$I->setupConvertKitPluginDisableJS($I);

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
		$I->testRestrictedContentByProductOnFrontend($I, $postID);
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
		// Setup ConvertKit Plugin, disabling JS.
		$I->setupConvertKitPluginDisableJS($I);

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
			$I->testRestrictedContentByProductOnFrontend($I, $postID);
			$I->resetCookie('ck_subscriber_id');
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
