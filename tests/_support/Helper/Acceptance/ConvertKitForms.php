<?php
namespace Helper\Acceptance;

/**
 * Helper methods and actions related to the ConvertKit Plugin's Forms
 * functionality, which are then available using $I->{yourFunctionName}.
 *
 * @since   2.2.0
 */
class ConvertKitForms extends \Codeception\Module
{
	/**
	 * Check that expected HTML exists in the DOM of the page we're viewing for
	 * a Form Trigger block or shortcode, and that the button loads the expected
	 * ConvertKit Form.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I              Tester.
	 * @param   string           $formURL        Form URL.
	 * @param   bool|string      $text           Test if the button text matches the given value.
	 * @param   bool|string      $textColor      Test if the given text color is applied.
	 * @param   bool|string      $backgroundColor Test is the given background color is applied.
	 */
	public function seeFormTriggerOutput($I, $formURL, $text = false, $textColor = false, $backgroundColor = false)
	{
		// Confirm that the button stylesheet loaded.
		$I->seeInSource('<link rel="stylesheet" id="convertkit-button-css" href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/plugins/convertkit/resources/frontend/css/button.css');

		// Confirm that the block displays.
		$I->seeElementInDOM('a.convertkit-formtrigger.wp-block-button__link');

		// Confirm that the button links to the correct form.
		$I->assertEquals($formURL, $I->grabAttributeFrom('a.convertkit-formtrigger', 'href'));

		// Confirm that the text is as expected.
		if ($text !== false) {
			$I->see($text);
		}

		// Confirm that the text color is as expected.
		if ($textColor !== false) {
			$I->seeElementInDOM('a.convertkit-formtrigger.has-text-color');
			$I->assertStringContainsString(
				'color:' . $textColor,
				$I->grabAttributeFrom('a.convertkit-formtrigger', 'style')
			);
		}

		// Confirm that the background color is as expected.
		if ($backgroundColor !== false) {
			$I->seeElementInDOM('a.convertkit-formtrigger.has-background');
			$I->assertStringContainsString(
				'background-color:' . $backgroundColor,
				$I->grabAttributeFrom('a.convertkit-formtrigger', 'style')
			);
		}

		// Click the button to confirm that the ConvertKit modal displays.
		$I->click('a.convertkit-formtrigger');
		$I->waitForElementVisible('div.formkit-overlay');
	}

	/**
	 * Check that expected HTML does not exist in the DOM of the page we're viewing for
	 * a Form Trigger block or shortcode.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I      Tester.
	 */
	public function dontSeeFormTriggerOutput($I)
	{
		// Confirm that the block does not display.
		$I->dontSeeElementInDOM('div.wp-block-button a.convertkit-formtrigger');
	}

	/**
	 * Check that expected HTML exists in the DOM of the page we're viewing for
	 * a Form Trigger link, and that the link loads the expected
	 * ConvertKit Form.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I              Tester.
	 * @param   string           $formURL        Form URL.
	 * @param   bool|string      $text           Test if the text matches the given value.
	 */
	public function seeFormTriggerLinkOutput($I, $formURL, $text = false)
	{
		// Confirm that the link displays.
		$I->seeElementInDOM('a.convertkit-form-link');

		// Confirm that the button links to the correct form.
		$I->assertEquals($formURL, $I->grabAttributeFrom('a.convertkit-form-link', 'href'));

		// Confirm that the text is as expected.
		if ($text !== false) {
			$I->see($text);
		}

		// Click the link to confirm that the ConvertKit form displays.
		$I->click('a.convertkit-form-link');
		$I->waitForElementVisible('div.formkit-overlay');
	}

	/**
	 * Check that expected HTML does not exist in the DOM of the page we're viewing for
	 * a Form Trigger link formatter.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I      Tester.
	 */
	public function dontSeeFormTriggerLinkOutput($I)
	{
		// Confirm that the link does not display.
		$I->dontSeeElementInDOM('a.convertkit-form-link');
	}
}
