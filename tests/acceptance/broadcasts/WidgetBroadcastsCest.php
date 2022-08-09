<?php
/**
 * Tests for the ConvertKit Broadcasts block when used as a widget.
 * 
 * A widget area is typically defined by a Theme in a shared area, such as a sidebar or footer.
 * 
 * @since 	1.9.8.2
 */
class WidgetBroadcastsCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.8.2
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);

		// Activate an older WordPress Theme that supports Widgets.
		$I->useTheme('twentytwentyone');
	}

	/**
	 * Test the Broadcasts block works when using the default parameters.
	 * 
	 * @since 	1.9.8.2
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWidgetWithDefaultParameters(AcceptanceTester $I)
	{
		// Add block widget.
		$I->addBlockWidget($I, 'ConvertKit Broadcasts', 'convertkit-broadcasts');

		// View the home page.
		$I->amOnPage('/');

		// Confirm that the block displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the default date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">April 8, 2022</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [1,10]);
	}

	/**
	 * Test the Broadcasts block's date format parameter works.
	 * 
	 * @since 	1.9.8.2
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWidgetWithDateFormatParameter(AcceptanceTester $I)
	{
		// Add block widget.
		$I->addBlockWidget($I, 'ConvertKit Broadcasts', 'convertkit-broadcasts', [
			'date_format' => [ 'select', 'Y-m-d' ],
		]);

		// View the home page.
		$I->amOnPage('/');

		// Confirm that the block displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the date format is as expected.
		$I->seeInSource('<time datetime="2022-04-08">2022-04-08</time>');

		// Confirm that the default expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', [1,10]);
	}

	/**
	 * Test the Broadcasts block's limit parameter works.
	 * 
	 * @since 	1.9.8.2
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWidgetWithLimitParameter(AcceptanceTester $I)
	{
		// Add block widget.
		$I->addBlockWidget($I, 'ConvertKit Broadcasts', 'convertkit-broadcasts', [
			'limit' => [ 'input', '2' ],
		]);

		// View the home page.
		$I->amOnPage('/');

		// Confirm that the block displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', 2);
	}

	/**
	 * Test the Broadcasts block's pagination works when enabled.
	 * 
	 * @since 	1.9.8.2
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWidgetWithPaginationEnabled(AcceptanceTester $I)
	{
		// Add block widget.
		$I->addBlockWidget($I, 'ConvertKit Broadcasts', 'convertkit-broadcasts', [
			'.components-form-toggle' 	=> [ 'toggle', true ],
			'limit' 					=> [ 'input', '1' ],
		]);

		// View the home page.
		$I->amOnPage('/');

		// Test pagination.
		$I->testBroadcastsPagination($I, 'Previous', 'Next');
	}

	/**
	 * Test the Broadcasts block's pagination labels work when defined.
	 * 
	 * @since 	1.9.8.2
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsBlockWithPaginationLabelParameters(AcceptanceTester $I)
	{
		// Add block widget.
		$I->addBlockWidget($I, 'ConvertKit Broadcasts', 'convertkit-broadcasts', [
			'.components-form-toggle' 	=> [ 'toggle', true ],
			'limit' 	 		  		=> [ 'input', '1' ],
			'paginate_label_prev' 		=> [ 'input', 'Newer' ],
			'paginate_label_next' 		=> [ 'input', 'Older' ],
		]);

		// View the home page.
		$I->amOnPage('/');

		// Test pagination.
		$I->testBroadcastsPagination($I, 'Older', 'Newer');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.8.2
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		// Activate the current theme.
		$I->useTheme('twentytwentytwo');
		$I->resetWidgets($I);
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}