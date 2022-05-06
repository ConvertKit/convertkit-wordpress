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
		$I->enableDebugLog($I);
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
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: Default: None');

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', 'Default' ],
		]);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

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

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: Default');

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', 'Default' ],
		]);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

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

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: Legacy: Default');

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', 'Default' ],
		]);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

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
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: None');

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', 'None' ],
		]);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

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
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
		]);

		// Get Form ID.
		$formID = $I->grabValueFrom('#wp-convertkit-form');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

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
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME'] ],
		]);

		// Get Form ID.
		$formID = $I->grabValueFrom('#wp-convertkit-form');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the ConvertKit Legacy Form displays.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . '/subscribe" data-remote="true">');
	}

	/**
	 * Test that the Default Form for Pages displays when an invalid Form ID is specified
	 * for a Page.
	 * 
	 * Whilst the on screen options won't permit selecting an invalid Form ID, a Page might
	 * have an invalid Form ID because:
	 * - the form belongs to another ConvertKit account (i.e. API credentials were changed in the Plugin, but this Page's specified Form was not changed)
	 * - the form was deleted from the ConvertKit account.
	 * 
	 * @since 	1.9.7.2
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPageUsingInvalidDefinedForm(AcceptanceTester $I)
	{
		// Setup the Default Form for Pages and Posts.
		$I->setupConvertKitPluginDefaultForm($I);

		// Create Page, with an invalid Form ID, as if it were created prior to API credentials being changed and/or
		// a Form being deleted in ConvertKit.
		$postID = $I->havePostInDatabase([
			'post_type' 	=> 'page',
			'post_title' 	=> 'ConvertKit: Form: Specific: Invalid',
			'meta_input'	=> [
				'_wp_convertkit_post_meta' => [
					'form'         => '11111',
					'landing_page' => '',
					'tag'          => '',
				]
			],
		]);

		// Load the Page on the frontend site.
		$I->amOnPage('/?p='.$postID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the invalid ConvertKit Form does not display.
		$I->dontSeeElementInDOM('form[data-sv-form="11111"]');

		// Confirm that the Default Form for Pages does display as a fallback.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}