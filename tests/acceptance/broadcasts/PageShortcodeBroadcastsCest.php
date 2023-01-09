<?php
/**
 * Tests for the ConvertKit Form shortcode.
 *
 * @since   1.9.7.4
 */
class PageShortcodeBroadcastsCest
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
		$I->setupConvertKitPlugin($I);
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode works when using the default parameters,
	 * using the Classic Editor (TinyMCE / Visual).
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsShortcodeInVisualEditorWithDefaultParameters(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Visual Editor');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			false,
			'[convertkit_broadcasts date_format="F j, Y" limit="10" paginate="0" paginate_label_prev="Previous" paginate_label_next="Next"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the shortcode output displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">April 8, 2022</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [ 1, 10 ]);
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode works when specifying a non-default date format parameter,
	 * using the Classic Editor (TinyMCE / Visual).
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsShortcodeInVisualEditorWithDateFormatParameter(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Visual Editor: Date Format');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			[
				'date_format' => [ 'select', date('Y-m-d') ],
			],
			'[convertkit_broadcasts date_format="Y-m-d" limit="10" paginate="0" paginate_label_prev="Previous" paginate_label_next="Next"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the shortcode output displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">2022-04-08</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [ 1, 10 ]);
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode works when specifying a non-default limit parameter,
	 * using the Classic Editor (TinyMCE / Visual).
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsShortcodeInVisualEditorWithLimitParameter(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Visual Editor: Limit');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			[
				'limit' => [ 'input', '2' ],
			],
			'[convertkit_broadcasts date_format="F j, Y" limit="2" paginate="0" paginate_label_prev="Previous" paginate_label_next="Next"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the shortcode output displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">April 8, 2022</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', 2);
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode pagination works when enabled,
	 * using the Classic Editor (TinyMCE / Visual).
	 *
	 * @since   1.9.7.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsShortcodeInVisualEditorWithPaginationEnabled(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Visual Editor: Pagination');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			[
				'limit'    => [ 'input', '1' ],
				'paginate' => [ 'toggle', 'Yes' ],
			],
			'[convertkit_broadcasts date_format="F j, Y" limit="1" paginate="1" paginate_label_prev="Previous" paginate_label_next="Next"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Test pagination.
		$I->testBroadcastsPagination($I, 'Previous', 'Next');
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode pagination labels display when defined,
	 * using the Classic Editor (TinyMCE / Visual).
	 *
	 * @since   1.9.7.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsShortcodeInVisualEditorWithPaginationLabelParameters(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Visual Editor: Pagination Labels');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			[
				'limit'               => [ 'input', '1' ],
				'paginate'            => [ 'toggle', 'Yes' ],
				'paginate_label_prev' => [ 'input', 'Newer' ],
				'paginate_label_next' => [ 'input', 'Older' ],
			],
			'[convertkit_broadcasts date_format="F j, Y" limit="1" paginate="1" paginate_label_prev="Newer" paginate_label_next="Older"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Test pagination.
		$I->testBroadcastsPagination($I, 'Older', 'Newer');
	}

	/**
	 * Test the [convertkit_broadcasts] default pagination labels display when not defined
	 * in the shortcode, using the Classic Editor (TinyMCE / Visual).
	 *
	 * @since   2.0.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsShortcodeInVisualEditorWithBlankPaginationLabelParameters(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Visual Editor: Blank Pagination Labels');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			[
				'limit'               => [ 'input', '1' ],
				'paginate'            => [ 'toggle', 'Yes' ],
				'paginate_label_prev' => [ 'input', '' ],
				'paginate_label_next' => [ 'input', '' ],
			],
			'[convertkit_broadcasts date_format="F j, Y" limit="1" paginate="1"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Test pagination.
		$I->testBroadcastsPagination($I, 'Previous', 'Next');
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode hex colors works when chosen,
	 * using the Classic Editor (TinyMCE / Visual).
	 *
	 * @since   1.9.8.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsShortcodeInVisualEditorWithHexColorParameters(AcceptanceTester $I)
	{
		// Define colors.
		$backgroundColor = '#ee1616';
		$textColor       = '#1212c0';
		$linkColor       = '#ffffff';

		// It's tricky to interact with WordPress's color picker, so we programmatically create the Page
		// instead to then confirm the color settings apply on the output.
		// We don't need to test the color picker itself, as it's a WordPress supplied component, and our
		// other Acceptance tests confirm that the shortcode can be added in the Classic Editor.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-page-broadcasts-shortcode-hex-color-params',
				'post_content' => '[convertkit_broadcasts date_format="F j, Y" limit="1" paginate="1" paginate_label_prev="Newer" paginate_label_next="Older" link_color="' . $linkColor . '" background_color="' . $backgroundColor . '" text_color="' . $textColor . '"]',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-broadcasts-shortcode-hex-color-params');

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
		$I->seeInSource('<a href="https://cheerful-architect-3237.ck.page/posts/paid-subscriber-broadcast?utm_source=wordpress&amp;utm_content=convertkit" target="_blank" rel="nofollow noopener" style="color:' . $linkColor . '"');

		// Test pagination.
		$I->testBroadcastsPagination($I, 'Older', 'Newer');

		// Confirm that link styles are still applied to refreshed data.
		$I->seeInSource('<a href="https://cheerful-architect-3237.ck.page/posts/paid-subscriber-broadcast?utm_source=wordpress&amp;utm_content=convertkit" target="_blank" rel="nofollow noopener" style="color:' . $linkColor . '"');
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode works when using the default parameters,
	 * using the Text Editor.
	 *
	 * @since   1.9.7.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsShortcodeInTextEditorWithDefaultParameters(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Text Editor');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addTextEditorShortcode(
			$I,
			'convertkit-broadcasts',
			false,
			'[convertkit_broadcasts date_format="F j, Y" limit="10" paginate="0" paginate_label_prev="Previous" paginate_label_next="Next"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the shortcode output displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">April 8, 2022</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [ 1, 10 ]);
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode works when specifying a non-default date format parameter,
	 * using the Text Editor.
	 *
	 * @since   1.9.7.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsShortcodeInTextEditorWithDateFormatParameter(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Text Editor: Date Format');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addTextEditorShortcode(
			$I,
			'convertkit-broadcasts',
			[
				'date_format' => [ 'select', date('Y-m-d') ],
			],
			'[convertkit_broadcasts date_format="Y-m-d" limit="10" paginate="0" paginate_label_prev="Previous" paginate_label_next="Next"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the shortcode output displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">2022-04-08</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [ 1, 10 ]);
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode works when specifying a non-default limit parameter,
	 * using the Text Editor.
	 *
	 * @since   1.9.7.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsShortcodeInTextEditorWithLimitParameter(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Text Editor: Limit');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addTextEditorShortcode(
			$I,
			'convertkit-broadcasts',
			[
				'limit' => [ 'input', '2' ],
			],
			'[convertkit_broadcasts date_format="F j, Y" limit="2" paginate="0" paginate_label_prev="Previous" paginate_label_next="Next"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the shortcode output displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">April 8, 2022</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', 2);
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode pagination works when enabled,
	 * using the Text Editor.
	 *
	 * @since   1.9.7.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsShortcodeInTextEditorWithPaginationEnabled(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Text Editor: Pagination');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addTextEditorShortcode(
			$I,
			'convertkit-broadcasts',
			[
				'limit'    => [ 'input', '1' ],
				'paginate' => [ 'toggle', 'Yes' ],
			],
			'[convertkit_broadcasts date_format="F j, Y" limit="1" paginate="1" paginate_label_prev="Previous" paginate_label_next="Next"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Test pagination.
		$I->testBroadcastsPagination($I, 'Previous', 'Next');
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode pagination works when enabled,
	 * using the Text Editor.
	 *
	 * @since   1.9.7.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsShortcodeInTextEditorWithPaginationLabelParameters(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Text Editor: Pagination Labels');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addTextEditorShortcode(
			$I,
			'convertkit-broadcasts',
			[
				'limit'               => [ 'input', '1' ],
				'paginate'            => [ 'toggle', 'Yes' ],
				'paginate_label_prev' => [ 'input', 'Newer' ],
				'paginate_label_next' => [ 'input', 'Older' ],
			],
			'[convertkit_broadcasts date_format="F j, Y" limit="1" paginate="1" paginate_label_prev="Newer" paginate_label_next="Older"]'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Test pagination.
		$I->testBroadcastsPagination($I, 'Older', 'Newer');
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode parameters are correctly escaped on output,
	 * to prevent XSS.
	 *
	 * @since   2.0.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBroadcastsShortcodeParameterEscaping(AcceptanceTester $I)
	{
		// Define a 'bad' shortcode.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-page-broadcasts-shortcode-parameter-escaping',
				'post_content' => '[convertkit_broadcasts date_format="F j, Y" limit="1" paginate="1" paginate_label_prev="Previous" paginate_label_next="Next" link_color=\'red" onmouseover="alert(1)"\']',
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-page-broadcasts-shortcode-parameter-escaping');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the output is escaped.
		$I->seeInSource('style="color:red&quot; onmouseover=&quot;alert(1)&quot;"');
		$I->dontSeeInSource('style="color:red" onmouseover="alert(1)""');

		// Test pagination.
		$I->testBroadcastsPagination($I, 'Previous', 'Next');

		// Confirm that the output is still escaped.
		$I->seeInSource('style="color:red&quot; onmouseover=&quot;alert(1)&quot;"');
		$I->dontSeeInSource('style="color:red" onmouseover="alert(1)""');
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
		$I->deactivateThirdPartyPlugin($I, 'classic-editor');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
