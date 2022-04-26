<?php
/**
 * Tests for the ConvertKit Form's Elementor Widget.
 * 
 * @since 	1.9.6
 */
class PageElementorFormCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->activateThirdPartyPlugin($I, 'elementor');
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);
		$I->wait(2);
	}

	/**
	 * Test the Form widget is registered in Elementor.
	 * 
	 * @since 	1.9.7.2
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testFormWidgetIsRegistered(AcceptanceTester $I)
	{
		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed
		$I->maybeCloseGutenbergWelcomeModal($I);

		// Change Form to None, so that no Plugin level Form is displayed, ensuring we only
		// test the Form block in Gutenberg.
		$I->selectOption('#wp-convertkit-form', 'None');

		// Define a Page Title.
		$I->fillField('.editor-post-title__input', 'ConvertKit: Page: Form: Elementor: Valid Form Param');

		// Click Edit with Elementor button.
		$I->click('#elementor-switch-mode-button');

		// When Elementor loads, search for the ConvertKit Form block.
		$I->waitForElementVisible('#elementor-panel-elements-search-input');
		$I->fillField('#elementor-panel-elements-search-input', 'ConvertKit Form');

		// Confirm that the Form widget is displayed as an option.
		$I->seeElementInDOM('#elementor-panel-elements .elementor-element');
	}

	/**
	 * Test the Form widget works when a valid Form is selected.
	 * 
	 * @since 	1.9.7.2
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testFormWidgetWithValidFormParameter(AcceptanceTester $I)
	{
		// Create Page with Form widget in Elementor.
		$pageID = $this->_createPageWithFormWidget($I, 'ConvertKit: Page: Form: Elementor Widget: Valid Form Param', $_ENV['CONVERTKIT_API_FORM_ID']);

		// Load Page.
		$I->amOnPage('?p='.$pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Form is displayed.
		$I->seeElementInDOM('form[data-sv-form="'.$_ENV['CONVERTKIT_API_FORM_ID'].'"]');
	}

	/**
	 * Test the Form widget works when a valid Legacy Form is selected.
	 * 
	 * @since 	1.9.7.2
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testFormWidgetWithValidLegacyFormParameter(AcceptanceTester $I)
	{
		// Create Page with Form widget in Elementor.
		$pageID = $this->_createPageWithFormWidget($I, 'ConvertKit: Legacy Form: Elementor Widget: Valid Form Param', $_ENV['CONVERTKIT_API_LEGACY_FORM_ID']);

		// Load Page.
		$I->amOnPage('?p='.$pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Form is displayed.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . '/subscribe" data-remote="true">');
	}

	/**
	 * Test the Form widget works when no Form is selected.
	 * 
	 * @since 	1.9.7.2
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testFormWidgetWithNoFormParameter(AcceptanceTester $I)
	{
		// Create Page with Form widget in Elementor.
		$pageID = $this->_createPageWithFormWidget($I, 'ConvertKit: Page: Form: Elementor Widget: No Form Param', '');

		// Load Page.
		$I->amOnPage('?p='.$pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that no ConvertKit Form is displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');
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
	 * testFormWidgetIsRegistered() above is a sanity check that the Form Widget is registered
	 * and available to users in Elementor.
	 * 
	 * @since 	1.9.7.2
	 * 
	 * @param 	AcceptanceTester 	$I 		Tester.
	 * @param 	string 				$title 	Page Title.
	 * @param 	int 				$formID ConvertKit Form ID.
	 * @return 	int 						Page ID
	 */
	private function _createPageWithFormWidget(AcceptanceTester $I, $title, $formID)
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
										'settings' => [
											'form' => (string) $formID,
										],
										'widgetType' => 'convertkit-elementor-form',
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
	 * @since 	1.9.6.7
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}