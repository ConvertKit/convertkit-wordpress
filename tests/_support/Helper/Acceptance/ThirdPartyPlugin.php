<?php
namespace Helper\Acceptance;

/**
 * Helper methods and actions related to third party Plugins,
 * which are then available using $I->{yourFunctionName}.
 *
 * @since   1.9.6
 */
class ThirdPartyPlugin extends \Codeception\Module
{
	/**
	 * Helper method to activate a third party Plugin, checking
	 * it activated and no errors were output.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I     			AcceptanceTester.
	 * @param   string           $name  			Plugin Slug.
	 * @param 	bool 			 $checkForWarnings 	Check for Xdebug warnings and notices on screen after activation.
	 */
	public function activateThirdPartyPlugin($I, $name, $checkForWarnings = true)
	{
		// Login as the Administrator.
		$I->loginAsAdmin();

		// Go to the Plugins screen in the WordPress Administration interface.
		$I->amOnPluginsPage();

		// Activate the Plugin.
		$I->activatePlugin($name);

		// Go to the Plugins screen again; this prevents any Plugin that loads a wizard-style screen from
		// causing seePluginActivated() to fail.
		$I->amOnPluginsPage();

		// Some Plugins have a different slug when activated.
		switch ($name) {
			case 'gravity-forms':
				$I->seePluginActivated('gravityforms');
				break;

			default:
				$I->seePluginActivated($name);
				break;
		}

		// Check that no PHP warnings or notices were output.
		if ( $checkForWarnings ) {
			$I->checkNoWarningsAndNoticesOnScreen($I);
		}
	}

	/**
	 * Helper method to activate a third party Plugin, checking
	 * it activated and no errors were output.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I      Acceptance Tester.
	 * @param   string           $name   Plugin Slug.
	 */
	public function deactivateThirdPartyPlugin($I, $name)
	{
		// Login as the Administrator.
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
