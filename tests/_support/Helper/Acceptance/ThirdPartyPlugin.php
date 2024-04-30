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
	 * @param   AcceptanceTester $I                 AcceptanceTester.
	 * @param   string           $name              Plugin Slug.
	 */
	public function activateThirdPartyPlugin($I, $name)
	{
		// Login as the Administrator.
		$I->loginAsAdmin();

		// Go to the Plugins screen in the WordPress Administration interface.
		$I->amOnPluginsPage();

		// Depending on the Plugin name, perform activation.
		switch ($name) {
			case 'woocommerce':
				// The bulk action to activate won't be available in WordPress 6.5 due to dependent
				// plugins being installed.
				// See https://core.trac.wordpress.org/ticket/60863.
				$I->click('a#activate-' . $name);
				break;

			default:
				// Activate the Plugin.
				$I->activatePlugin($name);
				break;
		}

		// Go to the Plugins screen again; this prevents any Plugin that loads a wizard-style screen from
		// causing seePluginActivated() to fail.
		$I->amOnPluginsPage();

		// Some Plugins redirect to a welcome screen on activation, so we can't reliably check they're activated.
		switch ($name) {
			case 'wpforms-lite':
				break;

			default:
				$I->seePluginActivated($name);
				break;
		}

		// Some Plugins throw warnings / errors on activation, so we can't reliably check for errors.
		if ($name === 'wishlist-member' && version_compare( phpversion(), '8.1', '>' )) {
			return;
		}

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
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

		// Depending on the Plugin name, perform activation.
		switch ($name) {
			case 'woocommerce':
				// The bulk action to deactivate won't be available in WordPress 6.5 due to dependent
				// plugins being installed.
				// See https://core.trac.wordpress.org/ticket/60863.
				$I->click('a#deactivate-' . $name);
				break;

			default:
				// Dectivate the Plugin.
				$I->deactivatePlugin($name);
				break;
		}

		// Check that the Plugin deactivated successfully.
		$I->seePluginDeactivated($name);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}
}
