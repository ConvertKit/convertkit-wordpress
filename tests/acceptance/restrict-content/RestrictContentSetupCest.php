<?php
/**
 * Tests Restrict Content's Setup functionality.
 *
 * @since   2.1.0
 */
class RestrictContentSetupCest
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
	}

	/**
	 * Test that the Add New Member Content button does not display on the Pages screen when no API keys are configured.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewMemberContentButtonNotDisplayedWhenNoAPIKeys(AcceptanceTester $I)
	{
		// Navigate to Pages.
		$I->amOnAdminPage('edit.php?post_type=page');

		// Check the button isn't displayed.
		$I->dontSeeElementInDOM('a.convertkit-action page-title-action');
	}

	/**
	 * Test that the Add New Member Content button does not display on the Posts screen.
	 *
	 * @since   2.3.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewMemberContentButtonNotDisplayedOnPosts(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPlugin($I);

		// Navigate to Posts.
		$I->amOnAdminPage('edit.php?post_type=post');

		// Check the button isn't displayed.
		$I->dontSeeElementInDOM('a.convertkit-action');
	}

	/**
	 * Test that the Dashboard submenu item for this wizard does not display when a
	 * third party Admin Menu editor type Plugin is installed and active.
	 *
	 * @since   2.3.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testNoMemberContentWizardDashboardSubmenuItem(AcceptanceTester $I)
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

		// Confirm no Member Content Dashboard Submenu item exists.
		$I->dontSeeInSource('<a href="options.php?page=convertkit-restrict-content-setup"></a>');
	}

	/**
	 * Test that the Add New Member Content wizard displays call to actions to add a Product or Tag in ConvertKit
	 * when the ConvertKit account has no Tags and Products.
	 *
	 * @since   2.3.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewMemberContentDisplaysCTAWhenNoResources(AcceptanceTester $I)
	{
		// Setup Plugin using API keys that have no resources.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY_NO_DATA'], $_ENV['CONVERTKIT_API_SECRET_NO_DATA']);

		// Navigate to Pages.
		$I->amOnAdminPage('edit.php?post_type=page');

		// Click Add New Member Content button.
		$I->click('Add New Member Content');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the expected buttons display linking to ConvertKit.
		$I->see('Create product');
		$I->see('Create tag');
		$I->seeInSource('<a href="https://app.convertkit.com/products/new/?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit"');
		$I->seeInSource('<a href="https://app.convertkit.com/subscribers/?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit"');

		// Update the Plugin to use API keys that have resources.
		$I->setupConvertKitPlugin($I);

		// Click the button to reload the wizard.
		$I->click('#convertkit-setup-wizard-footer a.button-primary');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the Download and Course buttons now display.
		$I->see('What type of content are you offering?');
		$I->see('Download');
		$I->see('Course');
	}

	/**
	 * Test that the Add New Member Content > Exit wizard link returns to the Pages screen.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewMemberContentExitWizardLink(AcceptanceTester $I)
	{
		// Setup Plugin and navigate to Add New Member Content screen.
		$this->_setupAndLoadAddNewMemberContentScreen($I);

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
		$I->see('Add New Member Content');
	}

	/**
	 * Test that the Add New Member Content > Downloads generates the expected Page
	 * and restricts content by the selected Product.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewMemberContentDownloadsByProduct(AcceptanceTester $I)
	{
		// Setup Plugin and navigate to Add New Member Content screen.
		$this->_setupAndLoadAddNewMemberContentScreen($I);

		// Click Downloads button.
		$I->click('Download');

		// Confirm the Configure Download screen is displayed.
		$I->see('Configure Download');

		// Enter a title and description.
		$I->fillField('title', 'ConvertKit: Member Content: Download');
		$I->fillField('description', 'Visible content.');

		// Confirm that the limit option is not visible, as this is only for courses.
		$I->dontSee('How many lessons does this course consist of?');

		// Restrict by Product.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-restrict_content-container', $_ENV['CONVERTKIT_API_PRODUCT_NAME']);

		// Click submit button.
		$I->click('Submit');

		// Wait for the WP_List_Table of Pages to load.
		$I->waitForElementVisible('tbody#the-list');

		// Confirm that one Page is listed in the WP_List_Table.
		$I->see('ConvertKit: Member Content: Download');
		$I->seeInSource('<span class="post-state">ConvertKit Member Content</span>');

		// Hover mouse over Post's table row.
		$I->moveMouseOver('tr.iedit');

		// Get link to Page.
		$url = $I->grabAttributeFrom('tr.iedit span.view a', 'href');

		// Test Restrict Content functionality.
		$I->testRestrictedContentByProductOnFrontend(
			$I,
			$url,
			'Visible content.',
			'The downloadable content (that is available when the visitor has paid for the ConvertKit product) goes here.'
		);
	}

	/**
	 * Test that the Add New Member Content > Course generates the expected Pages.
	 * and restricts content by the selected Product.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewMemberContentCourseByProduct(AcceptanceTester $I)
	{
		// Setup Plugin and navigate to Add New Member Content screen.
		$this->_setupAndLoadAddNewMemberContentScreen($I);

		// Click Course button.
		$I->click('Course');

		// Confirm the Configure Course screen is displayed.
		$I->see('Configure Course');

		// Enter a title, description and lesson count.
		$I->fillField('title', 'ConvertKit: Member Content: Course');
		$I->fillField('description', 'Visible content.');
		$I->fillField('number_of_pages', '3');

		// Restrict by Product.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-restrict_content-container', $_ENV['CONVERTKIT_API_PRODUCT_NAME']);

		// Click submit button.
		$I->click('Submit');

		// Wait for the WP_List_Table of Pages to load.
		$I->waitForElementVisible('tbody#the-list');

		// Confirm that four Pages are listed in the WP_List_Table.
		$I->see('ConvertKit: Member Content: Course');
		$I->see('— ConvertKit: Member Content: Course: 1/3');
		$I->see('— ConvertKit: Member Content: Course: 2/3');
		$I->see('— ConvertKit: Member Content: Course: 3/3');
		$I->see('ConvertKit Member Content | Parent Page: ConvertKit: Member Content: Course');

		// Hover mouse over Post's table row.
		$I->moveMouseOver('tr.iedit:first-child');

		// Wait for View link to be visible.
		$I->waitForElementVisible('tr.iedit:first-child span.view a');

		// Click View link.
		$I->click('tr.iedit:first-child span.view a');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Confirm the Start Course button exists.
		$I->see('Start Course');

		// Get URL to first restricted content page.
		$url = $I->grabAttributeFrom('.wp-block-button a', 'href');

		// Test Restrict Content functionality.
		$I->testRestrictedContentByProductOnFrontend(
			$I,
			$url,
			'Some introductory text about lesson 1',
			'Lesson 1 content (that is available when the visitor has paid for the ConvertKit product) goes here.'
		);

		// Test Next / Previous links.
		$I->click('Next Lesson');
		$I->waitForElementVisible('body.page-template-default');
		$I->see('ConvertKit: Member Content: Course: 2/3');
		$I->see('Some introductory text about lesson 2');
		$I->see('Lesson 2 content (that is available when the visitor has paid for the ConvertKit product) goes here');

		$I->click('Next Lesson');
		$I->waitForElementVisible('body.page-template-default');
		$I->see('ConvertKit: Member Content: Course: 3/3');
		$I->see('Some introductory text about lesson 3');
		$I->see('Lesson 3 content (that is available when the visitor has paid for the ConvertKit product) goes here');

		$I->click('Previous Lesson');
		$I->waitForElementVisible('body.page-template-default');
		$I->see('ConvertKit: Member Content: Course: 2/3');
		$I->see('Some introductory text about lesson 2');
		$I->see('Lesson 2 content (that is available when the visitor has paid for the ConvertKit product) goes here');

		$I->click('Previous Lesson');
		$I->waitForElementVisible('body.page-template-default');
		$I->see('ConvertKit: Member Content: Course: 1/3');
		$I->see('Some introductory text about lesson 1');
		$I->see('Lesson 1 content (that is available when the visitor has paid for the ConvertKit product) goes here');
	}

	/**
	 * Test that the Add New Member Content > Downloads generates the expected Page
	 * and restricts content by the selected Tag.
	 *
	 * @since   2.3.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewMemberContentDownloadsByTag(AcceptanceTester $I)
	{
		// Setup Plugin and navigate to Add New Member Content screen.
		$this->_setupAndLoadAddNewMemberContentScreen($I);

		// Click Downloads button.
		$I->click('Download');

		// Confirm the Configure Download screen is displayed.
		$I->see('Configure Download');

		// Enter a title and description.
		$I->fillField('title', 'ConvertKit: Member Content: Download: Tag');
		$I->fillField('description', 'Visible content.');

		// Confirm that the limit option is not visible, as this is only for courses.
		$I->dontSee('How many lessons does this course consist of?');

		// Restrict by Tag.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-restrict_content-container', $_ENV['CONVERTKIT_API_TAG_NAME']);

		// Click submit button.
		$I->click('Submit');

		// Wait for the WP_List_Table of Pages to load.
		$I->waitForElementVisible('tbody#the-list');

		// Confirm that one Page is listed in the WP_List_Table.
		$I->see('ConvertKit: Member Content: Download: Tag');
		$I->seeInSource('<span class="post-state">ConvertKit Member Content</span>');

		// Hover mouse over Post's table row.
		$I->moveMouseOver('tr.iedit');

		// Get link to Page.
		$url = $I->grabAttributeFrom('tr.iedit span.view a', 'href');

		// Test Restrict Content functionality.
		$I->testRestrictedContentByTagOnFrontend(
			$I,
			$url,
			$I->generateEmailAddress(),
			'Visible content.',
			'The downloadable content (that is available when the visitor has paid for the ConvertKit product) goes here.'
		);
	}

	/**
	 * Test that the Add New Member Content > Course generates the expected Pages
	 * and restricts content by the selected Tag.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewMemberContentCourseByTag(AcceptanceTester $I)
	{
		// Setup Plugin and navigate to Add New Member Content screen.
		$this->_setupAndLoadAddNewMemberContentScreen($I);

		// Click Course button.
		$I->click('Course');

		// Confirm the Configure Course screen is displayed.
		$I->see('Configure Course');

		// Enter a title, description and lesson count.
		$I->fillField('title', 'ConvertKit: Member Content: Course: Tag');
		$I->fillField('description', 'Visible content.');
		$I->fillField('number_of_pages', '3');

		// Restrict by Product.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-restrict_content-container', $_ENV['CONVERTKIT_API_TAG_NAME']);

		// Click submit button.
		$I->click('Submit');

		// Wait for the WP_List_Table of Pages to load.
		$I->waitForElementVisible('tbody#the-list');

		// Confirm that four Pages are listed in the WP_List_Table.
		$I->see('ConvertKit: Member Content: Course: Tag');
		$I->see('— ConvertKit: Member Content: Course: Tag: 1/3');
		$I->see('— ConvertKit: Member Content: Course: Tag: 2/3');
		$I->see('— ConvertKit: Member Content: Course: Tag: 3/3');
		$I->see('ConvertKit Member Content | Parent Page: ConvertKit: Member Content: Course: Tag');

		// Hover mouse over Post's table row.
		$I->moveMouseOver('tr.iedit:first-child');

		// Wait for View link to be visible.
		$I->waitForElementVisible('tr.iedit:first-child span.view a');

		// Click View link.
		$I->click('tr.iedit:first-child span.view a');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body.page-template-default');

		// Confirm the Start Course button exists.
		$I->see('Start Course');

		// Get URL to first restricted content page.
		$url = $I->grabAttributeFrom('.wp-block-button a', 'href');

		// Test Restrict Content functionality.
		$I->testRestrictedContentByTagOnFrontend(
			$I,
			$url,
			$I->generateEmailAddress(),
			'Some introductory text about lesson 1',
			'Lesson 1 content (that is available when the visitor has paid for the ConvertKit product) goes here.'
		);

		// Test Next / Previous links.
		$I->click('Next Lesson');
		$I->waitForElementVisible('body.page-template-default');
		$I->see('ConvertKit: Member Content: Course: Tag: 2/3');
		$I->see('Some introductory text about lesson 2');
		$I->see('Lesson 2 content (that is available when the visitor has paid for the ConvertKit product) goes here');

		$I->click('Next Lesson');
		$I->waitForElementVisible('body.page-template-default');
		$I->see('ConvertKit: Member Content: Course: Tag: 3/3');
		$I->see('Some introductory text about lesson 3');
		$I->see('Lesson 3 content (that is available when the visitor has paid for the ConvertKit product) goes here');

		$I->click('Previous Lesson');
		$I->waitForElementVisible('body.page-template-default');
		$I->see('ConvertKit: Member Content: Course: Tag: 2/3');
		$I->see('Some introductory text about lesson 2');
		$I->see('Lesson 2 content (that is available when the visitor has paid for the ConvertKit product) goes here');

		$I->click('Previous Lesson');
		$I->waitForElementVisible('body.page-template-default');
		$I->see('ConvertKit: Member Content: Course: Tag: 1/3');
		$I->see('Some introductory text about lesson 1');
		$I->see('Lesson 1 content (that is available when the visitor has paid for the ConvertKit product) goes here');
	}

	/**
	 * Sets up the ConvertKit Plugin, and starts the Setup Wizard for Member Content.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	private function _setupAndLoadAddNewMemberContentScreen(AcceptanceTester $I)
	{
		// Setup Plugin.
		$I->setupConvertKitPlugin($I);

		// Navigate to Pages.
		$I->amOnAdminPage('edit.php?post_type=page');

		// Click Add New Member Content button.
		$I->click('Add New Member Content');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
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
		// Clear cookies for next request.
		$I->resetCookie('ck_subscriber_id');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
