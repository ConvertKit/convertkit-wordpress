<?php
/**
 * Tests for ConvertKit Forms on WordPress Pages.
 * 
 * @since 	1.9.6
 */
class PageFormCest
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
		// Activate and Setup ConvertKit plugin
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);

		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed
		$I->maybeCloseGutenbergWelcomeModal($I);
	}

	/**
	 * Test that the 'Default' option for the Default Form setting in the Plugin Settings works when
	 * creating and viewing a new WordPress Page, and there is no Default Form specified in the Plugin
	 * settings.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPageUsingDefaultFormWithNoDefaultFormSpecifiedInPlugin(AcceptanceTester $I)
	{
		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-form');

		// Change Form to Default
		$I->selectOption('#wp-convertkit-form', 'Default');

		// Define a Page Title.
		$I->fillField('#post-title-0', 'ConvertKit: Form: Default: None');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Check the value of the Form field matches the input provided.
		$I->performOn( '.post-publish-panel__postpublish-buttons', function($I) {
			$I->seeOptionIsSelected('#wp-convertkit-form', 'Default');
		});

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-form-default-none');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that no ConvertKit Form is displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test that the Default Form specified in the Plugin Settings works when
	 * creating and viewing a new WordPress Page.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPageUsingDefaultForm(AcceptanceTester $I)
	{
		// Specify the Default Form in the Plugin Settings.
		$defaultFormID = $I->setupConvertKitPluginDefaultForm($I);

		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-form');

		// Change Form to Default
		$I->selectOption('#wp-convertkit-form', 'Default');

		// Define a Page Title.
		$I->fillField('#post-title-0', 'ConvertKit: Form: Default');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Check the value of the Form field matches the input provided.
		$I->performOn( '.post-publish-panel__postpublish-buttons', function($I) {
			$I->seeOptionIsSelected('#wp-convertkit-form', 'Default');
		});

		// Load the Page on the frontend site
		$I->amOnPage('/convertkit-form-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Default Form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $defaultFormID . '"]');
	}

	/**
	 * Test that the Default Legacy Form specified in the Plugin Settings works when
	 * creating and viewing a new WordPress Page.
	 * 
	 * @since 	1.9.6.3
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPageUsingDefaultLegacyForm(AcceptanceTester $I)
	{
		// Specify the Default Legacy Form in the Plugin Settings.
		$defaultLegacyFormID = $I->setupConvertKitPluginDefaultLegacyForm($I);

		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-form');

		// Change Form to Default
		$I->selectOption('#wp-convertkit-form', 'Default');

		// Define a Page Title.
		$I->fillField('#post-title-0', 'ConvertKit: Form: Legacy: Default');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Check the value of the Form field matches the input provided.
		$I->performOn( '.post-publish-panel__postpublish-buttons', function($I) {
			$I->seeOptionIsSelected('#wp-convertkit-form', 'Default');
		});

		// Load the Page on the frontend site
		$I->amOnPage('/convertkit-form-legacy-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Default Legacy Form displays.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $defaultLegacyFormID . '/subscribe" data-remote="true">');
	}

	/**
	 * Test that 'None' Form specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPageUsingNoForm(AcceptanceTester $I)
	{
		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-form');

		// Change Form to 'None'
		$I->selectOption('#wp-convertkit-form', 'None');

		// Define a Page Title.
		$I->fillField('#post-title-0', 'ConvertKit: Form: None');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');

		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Check the value of the Form field matches the input provided.
		$I->performOn( '.post-publish-panel__postpublish-buttons', function($I) {
			$I->seeOptionIsSelected('#wp-convertkit-form', 'None');
		});

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-form-none');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that no ConvertKit Form is displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test that the Form specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPageUsingDefinedForm(AcceptanceTester $I)
	{
		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-form');

		// Change Form to value specified in the .env file.
		$I->selectOption('#wp-convertkit-form', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Define a Page Title.
		$I->fillField('#post-title-0', 'ConvertKit: Form: Specific');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Check the value of the Form field matches the input provided.
		$I->performOn( '.post-publish-panel__postpublish-buttons', function($I) {
			$I->seeOptionIsSelected('#wp-convertkit-form', $_ENV['CONVERTKIT_API_FORM_NAME']);
		});

		// Get Form ID.
		$formID = $I->grabValueFrom('#wp-convertkit-form');;

		// Load the Page on the frontend site
		$I->amOnPage('/convertkit-form-specific');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $formID . '"]');
	}

	/**
	 * Test that the Legacy Form specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 * 
	 * @since 	1.9.6.3
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPageUsingDefinedLegacyForm(AcceptanceTester $I)
	{
		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-form');

		// Change Form to value specified in the .env file.
		$I->selectOption('#wp-convertkit-form', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);

		// Define a Page Title.
		$I->fillField('#post-title-0', 'ConvertKit: Form: Legacy: Specific');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Check the value of the Form field matches the input provided.
		$I->performOn( '.post-publish-panel__postpublish-buttons', function($I) {
			$I->seeOptionIsSelected('#wp-convertkit-form', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);
		});

		// Get Form ID.
		$formID = $I->grabValueFrom('#wp-convertkit-form');;

		// Load the Page on the frontend site
		$I->amOnPage('/convertkit-form-legacy-specific');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Default Legacy Form displays.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . '/subscribe" data-remote="true">');
	}
}