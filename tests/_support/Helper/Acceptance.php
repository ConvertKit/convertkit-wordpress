<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
	/**
	 * Helper method to assert that there are non PHP errors, warnings or notices output
	 * 
	 * @since 	1.0.0
	 */
	public function checkNoWarningsAndNoticesOnScreen($I)
	{
		// Check that the <body> class does not have a php-error class, which indicates a suppressed PHP function call error.
		$I->dontSeeElement('.php-error');

		// Check that no Xdebug errors exist.
		$I->dontSeeElement('.xdebug-error');
		$I->dontSeeElement('.xe-notice');
	}

	/**
	 * Helper method to assert that the field's value contains the given value.
	 * 
	 * @since 	1.0.0
	 */
	public function seeFieldContains($I, $element, $value)
	{
		$this->assertNotFalse(strpos($I->grabValueFrom($element), $value));
	}

	/**
	 * Helper method to close the Gutenberg "Welcome to the block editor" dialog, which
	 * might show for each Page/Post test performed due to there being no persistence
	 * remembering that the user dismissed the dialog.
	 * 
	 * @since 	1.0.0
	 */
	public function maybeCloseGutenbergWelcomeModal($I)
	{
		try {
			$I->performOn('.components-modal__screen-overlay', [
				'click' => '.components-modal__screen-overlay .components-modal__header button.components-button'
			], 3);
		} catch ( \Facebook\WebDriver\Exception\TimeoutException $e ) {
		}
	}

	/**
	 * Helper method to activate the Plugin.
	 * 
	 * @since 	1.0.0
	 */
	public function activateConvertKitPlugin($I)
	{
		// Login as the Administrator
		$I->loginAsAdmin();

		// Go to the Plugins screen in the WordPress Administration interface.
		$I->amOnPluginsPage();

		// Activate the Plugin.
		$I->activatePlugin('convertkit');

		// Check that the Plugin activated successfully.
		$I->seePluginActivated('convertkit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to deactivate the Plugin.
	 * 
	 * @since 	1.0.0
	 */
	public function deactivateConvertKitPlugin($I)
	{
		// Login as the Administrator
		$I->loginAsAdmin();

		// Go to the Plugins screen in the WordPress Administration interface.
		$I->amOnPluginsPage();

		// Deactivate the Plugin.
		$I->deactivatePlugin('convertkit');

		// Check that the Plugin deactivated successfully.
		$I->seePluginDeactivated('convertkit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to setup the Plugin's API Key and Secret.
	 * 
	 * @since 	1.0.0
	 */
	public function setupConvertKitPlugin($I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Complete API Fields.
		$I->fillField('_wp_convertkit_settings[api_key]', $_ENV['CONVERTKIT_API_KEY']);
		$I->fillField('_wp_convertkit_settings[api_secret]', $_ENV['CONVERTKIT_API_SECRET']);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_wp_convertkit_settings[api_key]', $_ENV['CONVERTKIT_API_KEY']);
		$I->seeInField('_wp_convertkit_settings[api_secret]', $_ENV['CONVERTKIT_API_SECRET']);
	}

	/**
	 * Helper method to setup the Plugin's Default Form setting for Pages and Posts.
	 * 
	 * @since 	1.0.0
	 */
	public function setupConvertKitPluginDefaultForm($I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Select Default Form for Pages and Posts.
		$I->selectOption('_wp_convertkit_settings[page_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->selectOption('_wp_convertkit_settings[post_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_wp_convertkit_settings[page_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->seeInField('_wp_convertkit_settings[post_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Return Form ID for Pages
		return $I->grabValueFrom('_wp_convertkit_settings[page_form]');
	}

	/**
	 * Helper method to setup the Plugin's Default Form setting for WooCommerce Products.
	 * 
	 * @since 	1.0.0
	 */
	public function setupConvertKitPluginDefaultFormForWooCommerceProducts($I)
	{
		// Go to the Plugin's Settings Screen.
		$I->loadConvertKitSettingsGeneralScreen($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Select option.
		$I->selectOption('_wp_convertkit_settings[product_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the fields match the inputs provided.
		$I->seeInField('_wp_convertkit_settings[product_form]', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Return Form ID
		return $I->grabValueFrom('_wp_convertkit_settings[product_form]');
	}

	/**
	 * Helper method to load the Plugin's Settings > General screen.
	 * 
	 * @since 	1.0.0
	 */
	public function loadConvertKitSettingsGeneralScreen($I)
	{
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to load the Plugin's Settings > Tools screen.
	 * 
	 * @since 	1.0.0
	 */
	public function loadConvertKitSettingsToolsScreen($I)
	{
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=tools');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}
}
