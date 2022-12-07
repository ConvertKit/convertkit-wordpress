<?php
/**
 * Tests for the ConvertKit Broadcasts Gutenberg Block.
 *
 * @since   1.9.7.4
 */
class PageBlockBroadcastsCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test the Broadcasts block outputs a message when no Broadcasts exist.
	 *
	 * @since   2.0.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsBlockWithNoBroadcasts(AcceptanceTester $I)
	{
		// Setup Plugin with API keys for ConvertKit Account that has no Broadcasts, and enable debug log.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY_NO_DATA'], $_ENV['CONVERTKIT_API_SECRET_NO_DATA']);
		$I->enableDebugLog($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Broadcasts: No Broadcasts');

		// Add block to Page.
		$I->addGutenbergBlock($I, 'ConvertKit Broadcasts', 'convertkit-broadcasts');

		// Confirm that the Broadcasts block tells the user that no Broadcasts exist in ConvertKit.
		$I->see(
			'No Broadcasts exist in ConvertKit. Send your first Broadcast in ConvertKit to see the link to it here.',
			[
				'css' => '.convertkit-no-content',
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that no ConvertKit Broadcasts are displayed.
		$I->dontSeeElementInDOM('div.convertkit-broadcasts');
	}

	/**
	 * Test the Broadcasts block works when using the default parameters.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsBlockWithDefaultParameters(AcceptanceTester $I)
	{
		// Setup Plugin and enable debug log.
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Broadcasts: Default Params');

		// Add block to Page.
		$I->addGutenbergBlock($I, 'ConvertKit Broadcasts', 'convertkit-broadcasts');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the block displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">April 8, 2022</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [ 1, 10 ]);
	}

	/**
	 * Test the Broadcasts block's date format parameter works.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsBlockWithDateFormatParameter(AcceptanceTester $I)
	{
		// Setup Plugin and enable debug log.
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Broadcasts: Date Format Param');

		// Add block to Page, setting the date format.
		$I->addGutenbergBlock(
			$I,
			'ConvertKit Broadcasts',
			'convertkit-broadcasts',
			[
				'date_format' => [ 'select', 'Y-m-d' ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the block displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">2022-04-08</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [ 1, 10 ]);
	}

	/**
	 * Test the Broadcasts block's limit parameter works.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsBlockWithLimitParameter(AcceptanceTester $I)
	{
		// Setup Plugin and enable debug log.
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Broadcasts: Limit Param');

		// Add block to Page, setting the limit.
		$I->addGutenbergBlock(
			$I,
			'ConvertKit Broadcasts',
			'convertkit-broadcasts',
			[
				'limit' => [ 'input', '2' ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the block displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', 2);
	}

	/**
	 * Test the Broadcasts block renders when the limit parameter is blank.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsBlockWithBlankLimitParameter(AcceptanceTester $I)
	{
		// Setup Plugin and enable debug log.
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Broadcasts: Blank Limit Param');

		// Add block to Page.
		$I->addGutenbergBlock($I, 'ConvertKit Broadcasts', 'convertkit-broadcasts');

		// When the sidebar appears, blank the limit parameter as the user might, by pressing the backspace key twice.
		$I->waitForElementVisible('.interface-interface-skeleton__sidebar[aria-label="Editor settings"]');
		$I->pressKey('#convertkit_broadcasts_limit', \Facebook\WebDriver\WebDriverKeys::BACKSPACE );
		$I->pressKey('#convertkit_broadcasts_limit', \Facebook\WebDriver\WebDriverKeys::BACKSPACE );

		// Confirm that the block did not encounter an error and fail to render.
		$I->checkGutenbergBlockHasNoErrors($I, 'ConvertKit Broadcasts');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the block displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [ 1, 10 ]);
	}

	/**
	 * Test the Broadcasts block's pagination works when enabled.
	 *
	 * @since   1.9.7.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsBlockWithPaginationEnabled(AcceptanceTester $I)
	{
		// Setup Plugin and enable debug log.
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Broadcasts: Pagination');

		// Add block to Page, setting the limit.
		$I->addGutenbergBlock(
			$I,
			'ConvertKit Broadcasts',
			'convertkit-broadcasts',
			[
				'limit'                   => [ 'input', '1' ],
				'.components-form-toggle' => [ 'toggle', true ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Test pagination.
		$I->testBroadcastsPagination($I, 'Previous', 'Next');
	}

	/**
	 * Test the Broadcasts block's pagination labels work when defined.
	 *
	 * @since   1.9.7.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsBlockWithPaginationLabelParameters(AcceptanceTester $I)
	{
		// Setup Plugin and enable debug log.
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Broadcasts: Pagination Labels');

		// Add block to Page, setting the limit.
		$I->addGutenbergBlock(
			$I,
			'ConvertKit Broadcasts',
			'convertkit-broadcasts',
			[
				'limit'                   => [ 'input', '1' ],
				'.components-form-toggle' => [ 'toggle', true ],
				'paginate_label_prev'     => [ 'input', 'Newer' ],
				'paginate_label_next'     => [ 'input', 'Older' ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Test pagination.
		$I->testBroadcastsPagination($I, 'Older', 'Newer');
	}

	/**
	 * Test the Broadcasts block's default pagination labels display when not defined in the block.
	 *
	 * @since   2.0.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsBlockWithBlankPaginationLabelParameters(AcceptanceTester $I)
	{
		// Setup Plugin and enable debug log.
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Broadcasts: Blank Pagination Labels');

		// Add block to Page, setting the limit.
		$I->addGutenbergBlock(
			$I,
			'ConvertKit Broadcasts',
			'convertkit-broadcasts',
			[
				'limit'                   => [ 'input', '1' ],
				'.components-form-toggle' => [ 'toggle', true ],
				'paginate_label_prev'     => [ 'input', '' ],
				'paginate_label_next'     => [ 'input', '' ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Test pagination.
		$I->testBroadcastsPagination($I, 'Previous', 'Next');
	}

	/**
	 * Test the Broadcasts block's theme color parameters works.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsBlockWithThemeColorParameters(AcceptanceTester $I)
	{
		// Setup Plugin and enable debug log.
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);

		// Define colors.
		$backgroundColor = 'white';
		$textColor       = 'purple';

		// It's tricky to interact with Gutenberg's color picker, so we programmatically create the Page
		// instead to then confirm the color settings apply on the output.
		// We don't need to test the color picker itself, as it's a Gutenberg supplied component, and our
		// other Acceptance tests confirm that the block can be added in Gutenberg etc.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-page-broadcasts-block-theme-color-params',
				'post_content' => '<!-- wp:convertkit/broadcasts {"backgroundColor":"' . $backgroundColor . '","textColor":"' . $textColor . '"} /-->',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-broadcasts-block-theme-color-params');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the block displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that our stylesheet loaded.
		$I->seeInSource('<link rel="stylesheet" id="convertkit-broadcasts-css" href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/plugins/convertkit/resources/frontend/css/broadcasts.css');

		// Confirm that the chosen colors are applied as CSS styles.
		$I->seeInSource('<div class="convertkit-broadcasts has-text-color has-' . $textColor . '-color has-background has-' . $backgroundColor . '-background-color"');
	}

	/**
	 * Test the Broadcasts block's hex color parameters works.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsBlockWithHexColorParameters(AcceptanceTester $I)
	{
		// Setup Plugin and enable debug log.
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);

		// Define colors.
		$backgroundColor = '#ee1616';
		$textColor       = '#1212c0';

		// It's tricky to interact with Gutenberg's color picker, so we programmatically create the Page
		// instead to then confirm the color settings apply on the output.
		// We don't need to test the color picker itself, as it's a Gutenberg supplied component, and our
		// other Acceptance tests confirm that the block can be added in Gutenberg etc.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-page-broadcasts-block-hex-color-params',
				'post_content' => '<!-- wp:convertkit/broadcasts {"date_format":"m/d/Y","limit":3,"style":{"color":{"text":"' . $textColor . '","background":"' . $backgroundColor . '"}}} /-->',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-broadcasts-block-hex-color-params');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the block displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that our stylesheet loaded.
		$I->seeInSource('<link rel="stylesheet" id="convertkit-broadcasts-css" href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/plugins/convertkit/resources/frontend/css/broadcasts.css');

		// Confirm that the chosen colors are applied as CSS styles.
		$I->seeInSource('<div class="convertkit-broadcasts has-text-color has-background" style="color:' . $textColor . ';background-color:' . $backgroundColor . '"');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
