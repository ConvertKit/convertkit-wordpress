<?php
/**
 * Tests for the ConvertKit Broadcast's Elementor Widget.
 * 
 * @since 	1.9.7.8
 */
class ElementorBroadcastsCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.7.8
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->activateThirdPartyPlugin($I, 'elementor');
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);
	}

	/**
	 * Test the Broadcasts widget is registered in Elementor.
	 * 
	 * @since 	1.9.7.8
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsWidgetIsRegistered(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Broadcasts: Elementor: Registered');

		// Click Edit with Elementor button.
		$I->click('#elementor-switch-mode-button');

		// When Elementor loads, search for the ConvertKit Broadcasts block.
		$I->waitForElementVisible('#elementor-panel-elements-search-input');
		$I->fillField('#elementor-panel-elements-search-input', 'ConvertKit Broadcasts');

		// Confirm that the Broadcasts widget is displayed as an option.
		$I->seeElementInDOM('#elementor-panel-elements .elementor-element');
	}

	/**
	 * Test the Broadcasts block works when using valid parameters.
	 * 
	 * @since 	1.9.7.8
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsWidgetWithValidParameters(AcceptanceTester $I)
	{
		// Create Page with Broadcasts widget in Elementor.
		$pageID = $this->_createPageWithBroadcastsWidget($I, 'ConvertKit: Page: Broadcasts: Elementor Widget: Valid Params', [
			'date_format' 	=> 'F j, Y',
			'limit' 		=> 10,
		]);

		// Load Page.
		$I->amOnPage('?p='.$pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

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
	 * @since 	1.9.7.8
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsWidgetWithDateFormatParameter(AcceptanceTester $I)
	{
		// Create Page with Broadcasts widget in Elementor.
		$pageID = $this->_createPageWithBroadcastsWidget($I, 'ConvertKit: Page: Broadcasts: Elementor Widget: Date Format', [
			'date_format' => 'Y-m-d',
			'limit' 	  => 10,
		]);

		// Load Page.
		$I->amOnPage('?p='.$pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

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
	 * @since 	1.9.7.8
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsWidgetWithLimitParameter(AcceptanceTester $I)
	{
		// Create Page with Broadcasts widget in Elementor.
		$pageID = $this->_createPageWithBroadcastsWidget($I, 'ConvertKit: Page: Broadcasts: Elementor Widget: Limit', [
			'date_format' 	=> 'F j, Y',
			'limit' 		=> 2,
		]);

		// Load Page.
		$I->amOnPage('?p='.$pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the block displays.
		$I->seeBroadcastsOutput($I);

		// Confirm that the expected number of Broadcasts are displayed.
		$I->seeNumberOfElements('li.convertkit-broadcast', 2);
	}

	/**
	 * Test the Broadcasts block's pagination works when enabled.
	 * 
	 * @since 	1.9.7.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsWidgetWithPaginationEnabled(AcceptanceTester $I)
	{
		// Create Page with Broadcasts widget in Elementor.
		$pageID = $this->_createPageWithBroadcastsWidget($I, 'ConvertKit: Page: Broadcasts: Elementor Widget: Pagination', [
			'date_format' 	=> 'F j, Y',
			'limit' 		=> 1,
			'paginate' 		=> 1,
		]);

		// Load Page.
		$I->amOnPage('?p='.$pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Test pagination.
		$I->testBroadcastsPagination($I, 'Previous', 'Next');
	}

	/**
	 * Test the Broadcasts block's pagination labels work when defined.
	 * 
	 * @since 	1.9.7.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testBroadcastsWidgetWithPaginationLabelParameters(AcceptanceTester $I)
	{
		// Create Page with Broadcasts widget in Elementor.
		$pageID = $this->_createPageWithBroadcastsWidget($I, 'ConvertKit: Page: Broadcasts: Elementor Widget: Valid Params', [
			'date_format' 			=> 'F j, Y',
			'limit' 				=> 1,
			'paginate' 				=> 1,
			'paginate_label_prev' 	=> 'Newer',
			'paginate_label_next' 	=> 'Older',
		]);

		// Load Page.
		$I->amOnPage('?p='.$pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Test pagination.
		$I->testBroadcastsPagination($I, 'Older', 'Newer');
	}

	/**
	 * Create a Page in the database comprising of Elementor Page Builder data
	 * containing a ConvertKit Form widget.
	 * 
	 * Codeception's dragAndDrop() method doesn't support dropping an element into an iframe, which is
	 * how Elementor works for adding widgets to a Page.
	 * 
	 * Therefore, we directly create a Page in the database, with Elementor's data structure
	 * as if we added the Form widget to a Page edited in Elementor.
	 * 
	 * testBroadcastsWidgetIsRegistered() above is a sanity check that the Form Widget is registered
	 * and available to users in Elementor.
	 * 
	 * @since 	1.9.7.8
	 * 
	 * @param 	AcceptanceTester 	$I 			Tester.
	 * @param 	string 				$title 		Page Title.
	 * @param 	array 				$settings 	Widget settings.
	 * @return 	int 							Page ID
	 */
	private function _createPageWithBroadcastsWidget(AcceptanceTester $I, $title, $settings)
	{
		return $I->havePostInDatabase([
			'post_title'	=> $title,
			'post_type'		=> 'page',
			'post_status'	=> 'publish',
			'meta_input' => [
				// Elementor.
				'_elementor_data' => [
					0 => [
						'id' => '39bb59d',
						'elType' => 'section',
						'settings' => [],
						'elements' => [
							[
								'id' => 'b7e0e57',
								'elType' => 'column',
								'settings' => [
									'_column_size' => 100,
									'_inline_size' => null,
								],
								'elements' => [
									[
										'id' => 'a73a905',
										'elType' => 'widget',
										'settings' => $settings,
										'widgetType' => 'convertkit-elementor-broadcasts',
									],
								],
							],
						],
					],
				],
				'_elementor_version' => '3.6.1',
				'_elementor_edit_mode' => 'builder',
				'_elementor_template_type' => 'wp-page',

				// Configure ConvertKit Plugin to not display a default Form,
				// as we are testing for the Form in Elementor.
				'_wp_convertkit_post_meta' => [
					'form'         => '-1',
					'landing_page' => '',
					'tag'          => '',
				],
			],
		]);
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.7.8
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateThirdPartyPlugin($I, 'elementor');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}