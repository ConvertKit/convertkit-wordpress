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
		// Save settings.
		$I->setupConvertKitPluginRestrictContent($I);
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
