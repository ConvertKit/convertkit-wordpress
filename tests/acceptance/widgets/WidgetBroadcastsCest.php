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
			'limit' 					=> [ 'input', '1' ],
			'.components-form-toggle' 	=> [ 'toggle', true ],
		]);

		// View the home page.
		$I->amOnPage('/');

		// Test pagination.
		$I->testBroadcastsPagination($I, 'Previous', 'Next');
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