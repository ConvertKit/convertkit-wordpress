<?php
/**
 * Tests Restrict Content's Settings functionality at Settings > ConvertKit > Member Content.
 *
 * @since   2.1.0
 */
class RestrictContentSettingsCest
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
		// Activate ConvertKit Plugin.
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);
	}

	/**
	 * Tests that saving the default labels, with no changes, works with no errors.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSaveDefaultSettings(AcceptanceTester $I)
	{
		// Define visible and member only content.
		$visibleContent = 'Visible content.';
		$memberContent  = 'Member only content.';

		// Save settings.
		$I->setupConvertKitPluginRestrictContent($I);

		// Confirm default values were saved and display in the form fields.
		$defaults = $I->getRestrictedContentDefaultSettings();
		foreach ( $defaults as $key => $value ) {
			$I->seeInField('_wp_convertkit_settings_restrict_content[' . $key . ']', $value);
		}

		// Create Restricted Content Page.
		$pageID = $I->createRestrictedContentPage(
			$I,
			'ConvertKit: Restrict Content: Settings',
			$visibleContent,
			$memberContent,
			'product_' . $_ENV['CONVERTKIT_API_PRODUCT_ID']
		);

		// Test Restrict Content functionality.
		$I->testRestrictedContentOnFrontend($I, $pageID, $visibleContent, $memberContent);
	}

	/**
	 * Tests that saving blank labels results in the default labels being used when viewing
	 * a Restricted Content Page.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSaveBlankSettings(AcceptanceTester $I)
	{
		// Define visible and member only content.
		$visibleContent = 'Visible content.';
		$memberContent  = 'Member only content.';

		// Define settings.
		$settings = array(
			'subscribe_text'         => '',
			'subscribe_button_label' => '',
			'email_text'             => '',
			'email_button_label'     => '',
			'email_check_text'       => '',
		);

		// Save settings.
		$I->setupConvertKitPluginRestrictContent($I, $settings);

		// Confirm default values were saved and display in the form fields.
		$defaults = $I->getRestrictedContentDefaultSettings();
		foreach ( $defaults as $key => $value ) {
			$I->seeInField('_wp_convertkit_settings_restrict_content[' . $key . ']', $value);
		}

		// Create Restricted Content Page.
		$pageID = $I->createRestrictedContentPage(
			$I,
			'ConvertKit: Restrict Content: Settings: Blank',
			$visibleContent,
			$memberContent,
			'product_' . $_ENV['CONVERTKIT_API_PRODUCT_ID']
		);

		// Test Restrict Content functionality.
		$I->testRestrictedContentOnFrontend($I, $pageID, $visibleContent, $memberContent);
	}

	/**
	 * Tests that saving custom labels results in the settings labels being used when viewing
	 * a Restricted Content Page.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSaveSettings(AcceptanceTester $I)
	{
		// Define visible and member only content.
		$visibleContent = 'Visible content.';
		$memberContent  = 'Member only content.';

		// Define settings.
		$settings = array(
			'subscribe_text'         => 'Subscribe Text',
			'subscribe_button_label' => 'Subscribe Button Label',
			'email_text'             => 'Email Text',
			'email_button_label'     => 'Email Button Label',
			'email_check_text'       => 'Email Check Text',
		);

		// Save settings.
		$I->setupConvertKitPluginRestrictContent($I, $settings);

		// Confirm custom values were saved and display in the form fields.
		foreach ( $settings as $key => $value ) {
			$I->seeInField('_wp_convertkit_settings_restrict_content[' . $key . ']', $value);
		}

		// Create Restricted Content Page.
		$pageID = $I->createRestrictedContentPage(
			$I,
			'ConvertKit: Restrict Content: Settings: Custom',
			$visibleContent,
			$memberContent,
			'product_' . $_ENV['CONVERTKIT_API_PRODUCT_ID']
		);

		// Test Restrict Content functionality.
		$I->testRestrictedContentOnFrontend($I, $pageID, $visibleContent, $memberContent, $settings);
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
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
