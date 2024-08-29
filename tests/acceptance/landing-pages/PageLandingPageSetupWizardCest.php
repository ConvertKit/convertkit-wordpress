<?php
/**
 * Tests the Landing Page Setup Wizard functionality.
 *
 * @since   2.5.5
 */
class PageLandingPageSetupWizardCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.5.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate ConvertKit Plugin.
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test that the Add New Landing Page button does not display on the Pages screen when no ConvertKit
	 * account is connected.
	 *
	 * @since   2.5.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewLandingPageButtonNotDisplayedWhenNoCredentials(AcceptanceTester $I)
	{
		// Navigate to Pages.
		$I->amOnAdminPage('edit.php?post_type=page');

		// Check the buttons are not displayed.
		$I->dontSeeElementInDOM('span.convertkit-action.page-title-action');
	}

	/**
	 * Test that the Add New Landing Page button does not display on the Posts screen.
	 *
	 * @since   2.5.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewLandingPageButtonNotDisplayedOnPosts(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPlugin($I);

		// Navigate to Posts.
		$I->amOnAdminPage('edit.php?post_type=post');

		// Check the buttons are not displayed.
		$I->dontSeeElementInDOM('span.convertkit-action.page-title-action');
	}

	/**
	 * Test that the Dashboard submenu item for this wizard does not display when a
	 * third party Admin Menu editor type Plugin is installed and active.
	 *
	 * @since   2.5.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testNoLandingPageWizardDashboardSubmenuItem(AcceptanceTester $I)
	{
		// Activate Admin Menu Editor Plugin.
		$I->activateThirdPartyPlugin($I, 'admin-menu-editor');

		// Setup Plugin.
		$I->setupConvertKitPlugin($I);

		// Navigate to Admin Menu Editor's settings.
		$I->amOnAdminPage('options-general.php?page=menu_editor');

		// Save settings. If hiding submenu items fails in the Plugin, this step
		// will display those submenu items on subsequent page loads.
		$I->click('Save Changes');

		// Navigate to Dashboard.
		$I->amOnAdminPage('index.php');

		// Confirm no Landing Page Dashboard Submenu item exists.
		$I->dontSeeInSource('<a href="options.php?page=convertkit-landing-page-setup"></a>');
	}

	/**
	 * Test that the Add New Landing Page wizard displays call to actions to add a Landing Page in ConvertKit
	 * when the ConvertKit account has no Landing Pages
	 *
	 * @since   2.3.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewLandingPageDisplaysCTAWhenNoLandingPagesExist(AcceptanceTester $I)
	{
		// Setup Plugin using ConvertKit account that has no resources.
		$I->setupConvertKitPluginCredentialsNoData($I);

		// Navigate to Pages.
		$I->amOnAdminPage('edit.php?post_type=page');

		// Click Add New Landing Page button.
		$I->moveMouseOver('span.convertkit-action');
		$I->waitForElementVisible('span.convertkit-action span.convertkit-actions a');
		$I->click('Landing Page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the expected buttons display linking to ConvertKit.
		$I->see('Create landing page');
		$I->seeInSource('<a href="https://app.convertkit.com/pages/new/?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit"');

		// Update the Plugin to use credentials that have resources.
		$I->setupConvertKitPlugin($I);

		// Click the button to reload the wizard.
		$I->click('I\'ve created a landing page in ConvertKit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the setup fields now display.
		$I->see('Which landing page would you like to display?');
		$I->seeElementInDOM('select#landing_page');
		$I->seeElementInDOM('input#post_name');
	}

	/**
	 * Test that the Add New Landing Page > Exit wizard link returns to the Pages screen.
	 *
	 * @since   2.5.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewLandingPageExitWizardLink(AcceptanceTester $I)
	{
		// Setup Plugin and navigate to Add New Landing Page screen.
		$this->_setupAndLoadAddNewLandingPageScreen($I);

		// Click Exit wizard link.
		$I->click('Exit wizard');

		// Confirm exit.
		$I->acceptPopup();

		// Wait for the WP_List_Table of Pages to load.
		$I->waitForElementVisible('tbody#the-list');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm the Pages screen is displayed.
		$I->see('Pages');
		$I->moveMouseOver('span.convertkit-action');
		$I->waitForElementVisible('span.convertkit-action span.convertkit-actions a');
		$I->see('Landing Page');
	}

	/**
	 * Test that the Add New Landing Page generates the expected Page
	 * and displays the selected Landing Page.
	 *
	 * @since   2.5.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewMemberLandingPage(AcceptanceTester $I)
	{
		// Setup Plugin and navigate to Add New Landing Page screen.
		$this->_setupAndLoadAddNewLandingPageScreen($I);

		// Select a landing page and enter a slug.
		$I->fillSelect2Field($I, '#select2-landing_page-container', $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME']);
		$I->fillField('post_name', 'landing-page-setup-wizard');

		// Click create button.
		$I->click('Create');

		// Confirm that setup completed.
		$I->see('Setup complete');

		// Click the button to view the landing page.
		$I->click('View landing page');

		// Confirm that the basic HTML structure is correct.
		$this->_seeBasicHTMLStructure($I);

		// Confirm the ConvertKit Site Icon displays.
		$I->seeInSource('<link rel="shortcut icon" type="image/x-icon" href="https://pages.convertkit.com/templates/favicon.ico">');

		// Confirm that the ConvertKit Landing Page displays.
		$I->dontSeeElementInDOM('body.page'); // WordPress didn't load its template, which is correct.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_LANDING_PAGE_ID'] . '"]'); // ConvertKit injected its Landing Page Form, which is correct.
	}

	/**
	 * Test that the Add New Landing Page generates the expected Page
	 * and displays the selected Legacy Landing Page.
	 *
	 * @since   2.5.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewMemberLegacyLandingPage(AcceptanceTester $I)
	{
		// Setup Plugin and navigate to Add New Landing Page screen.
		$this->_setupAndLoadAddNewLandingPageScreen($I);

		// Select a landing page and enter a slug.
		$I->fillSelect2Field($I, '#select2-landing_page-container', $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_NAME']);
		$I->fillField('post_name', 'landing-page-setup-wizard');

		// Click create button.
		$I->click('Create');

		// Confirm that setup completed.
		$I->see('Setup complete');

		// Click the button to view the landing page.
		$I->click('View landing page');

		// Confirm that the basic HTML structure is correct.
		$this->_seeBasicHTMLStructure($I);

		// Confirm that the ConvertKit Landing Page displays.
		$I->dontSeeElementInDOM('body.page'); // WordPress didn't load its template, which is correct.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://app.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_ID'] . '/subscribe" data-remote="true">'); // ConvertKit injected its Landing Page Form, which is correct.
	}

	/**
	 * Sets up the ConvertKit Plugin, and starts the Setup Wizard for Landing Pages.
	 *
	 * @since   2.5.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	private function _setupAndLoadAddNewLandingPageScreen(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin, disabling JS.
		$I->setupConvertKitPluginDisableJS($I);

		// Navigate to Pages.
		$I->amOnAdminPage('edit.php?post_type=page');

		// Click Add New Landing Page button.
		$I->moveMouseOver('span.convertkit-action');
		$I->waitForElementVisible('span.convertkit-action span.convertkit-actions a');
		$I->click('Landing Page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to assert that the expected landing page HTML is output.
	 *
	 * @since   2.5.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	private function _seeBasicHTMLStructure($I)
	{
		$I->seeInSource('<html>');
		$I->seeInSource('<head>');
		$I->seeInSource('</head>');
		$I->seeInSource('<body');
		$I->seeInSource('</body>');
		$I->seeInSource('</html>');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.5.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
