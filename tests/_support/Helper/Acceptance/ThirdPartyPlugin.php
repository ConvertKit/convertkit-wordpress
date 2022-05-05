<?php
namespace Helper\Acceptance;

// Define any custom actions related to third party Plugins that
// would be used across multiple tests.
// These are then available in $I->{yourFunctionName}

class ThirdPartyPlugin extends \Codeception\Module
{
	/**
	 * Helper method to activate a third party Plugin, checking
	 * it activated and no errors were output.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	string 	$name 	Plugin Slug.
	 */
	public function activateThirdPartyPlugin($I, $name)
	{
		// Login as the Administrator
		$I->loginAsAdmin();

		// Go to the Plugins screen in the WordPress Administration interface.
		$I->amOnPluginsPage();

		// Activate the Plugin.
		$I->activatePlugin($name);

		// Check that the Plugin activated successfully.
		$I->seePluginActivated($name);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to activate a third party Plugin, checking
	 * it activated and no errors were output.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	string 	$name 	Plugin Slug.
	 */
	public function deactivateThirdPartyPlugin($I, $name)
	{
		// Login as the Administrator
		$I->loginAsAdmin();

		// Go to the Plugins screen in the WordPress Administration interface.
		$I->amOnPluginsPage();

		// Deactivate the Plugin.
		$I->deactivatePlugin($name);

		// Check that the Plugin deactivated successfully.
		$I->seePluginDeactivated($name);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}
}
