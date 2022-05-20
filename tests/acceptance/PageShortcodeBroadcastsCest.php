<?php
/**
 * Tests for the ConvertKit Form shortcode.
 * 
 * @since 	1.9.7.4
 */
class PageShortcodeBroadcastsCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode works when using the default parameters,
	 * using the Classic Editor (TinyMCE / Visual).
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsShortcodeInVisualEditorWithDefaultParameters(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Visual Editor');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			'convertkit-broadcasts',
			false,
			'[convertkit_broadcasts date_format="F j, Y" limit="10"'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the shortcode output displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">April 8, 2022</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [1,10]);
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode works when specifying a non-default date format parameter,
	 * using the Classic Editor (TinyMCE / Visual).
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsShortcodeInVisualEditorWithDateFormatParameter(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Visual Editor: Date Format');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			'convertkit-broadcasts',
			[
				'date_format' => [ 'select', date('Y-m-d') ],
			],
			'[convertkit_broadcasts date_format="Y-m-d" limit="10"'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the shortcode output displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">2022-04-08</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [1,10]);
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode works when specifying a non-default limit parameter,
	 * using the Classic Editor (TinyMCE / Visual).
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsShortcodeInVisualEditorWithLimitParameter(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Visual Editor: Limit');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			'convertkit-broadcasts',
			[
				'limit' => [ 'input', '2' ],
			],
			'[convertkit_broadcasts date_format="F j, Y" limit="2"'
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
	 * @since 	1.9.7.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsShortcodeWithPaginationEnabled(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Visual Editor: Pagination');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			'convertkit-broadcasts',
			[
				'limit' 	=> [ 'input', '1' ],
				'paginate' 	=> [ 'toggle', 'Yes' ],
			],
			'[convertkit_broadcasts date_format="F j, Y" limit="1" paginate="1"'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Test pagination.
		$I->testBroadcastsPagination($I, '< Previous', 'Next >');
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode pagination works when enabled,
	 * using the Classic Editor (TinyMCE / Visual).
	 * 
	 * @since 	1.9.7.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsShortcodeWithPaginationLabelParameters(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Visual Editor: Pagination Labels');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addVisualEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			'convertkit-broadcasts',
			[
				'limit' 			  => [ 'input', '1' ],
				'paginate' 			  => [ 'toggle', 'Yes' ],
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
	 * Test the [convertkit_broadcasts] shortcode works when using the default parameters,
	 * using the Text Editor.
	 * 
	 * @since 	1.9.7.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsShortcodeInTextEditorWithDefaultParameters(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Text Editor');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addTextEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			'convertkit-broadcasts',
			false,
			'[convertkit_broadcasts date_format="F j, Y" limit="10"'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the shortcode output displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">April 8, 2022</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [1,10]);
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode works when specifying a non-default date format parameter,
	 * using the Text Editor.
	 * 
	 * @since 	1.9.7.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsShortcodeInTextEditorWithDateFormatParameter(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Text Editor: Date Format');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addTextEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			'convertkit-broadcasts',
			[
				'date_format' => [ 'select', date('Y-m-d') ],
			],
			'[convertkit_broadcasts date_format="Y-m-d" limit="10"'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Confirm that the shortcode output displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">2022-04-08</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [1,10]);
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode works when specifying a non-default limit parameter,
	 * using the Text Editor.
	 * 
	 * @since 	1.9.7.5
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsShortcodeInTextEditorWithLimitParameter(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Text Editor: Limit');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addTextEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			'convertkit-broadcasts',
			[
				'limit' => [ 'input', '2' ],
			],
			'[convertkit_broadcasts date_format="F j, Y" limit="2"'
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
	 * @since 	1.9.7.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsShortcodeInTextEditorWithPaginationEnabled(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Text Editor: Pagination');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addTextEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			'convertkit-broadcasts',
			[
				'limit' 	=> [ 'input', '1' ],
				'paginate' 	=> [ 'toggle', 'Yes' ],
			],
			'[convertkit_broadcasts date_format="F j, Y" limit="1" paginate="1"'
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewClassicEditorPage($I);

		// Test pagination.
		$I->testBroadcastsPagination($I, '< Previous', 'Next >');
	}

	/**
	 * Test the [convertkit_broadcasts] shortcode pagination works when enabled,
	 * using the Text Editor.
	 * 
	 * @since 	1.9.7.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsShortcodeInTextEditorWithPaginationLabelParameters(AcceptanceTester $I)
	{
		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Broadcasts: Shortcode: Text Editor: Pagination Labels');

		// Add shortcode to Page, setting the Form setting to the value specified in the .env file.
		$I->addTextEditorShortcode(
			$I,
			'ConvertKit Broadcasts',
			'convertkit-broadcasts',
			[
				'limit' 			  => [ 'input', '1' ],
				'paginate' 			  => [ 'toggle', 'Yes' ],
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
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateThirdPartyPlugin($I, 'classic-editor');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}