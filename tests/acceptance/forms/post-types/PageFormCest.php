<?php
/**
 * Tests for ConvertKit Forms on WordPress Pages.
 *
 * @since   1.9.6
 */
class PageFormCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate ConvertKit plugin.
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test that the Pages > Add New screen has expected a11y output, such as label[for].
	 *
	 * @since   1.9.7.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAccessibility(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Navigate to Post Type (e.g. Pages / Posts) > Add New.
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Confirm that settings have label[for] attributes.
		$I->seeInSource('<label for="wp-convertkit-form">');
		$I->seeInSource('<label for="wp-convertkit-landing_page">');
		$I->seeInSource('<label for="wp-convertkit-tag">');
	}

	/**
	 * Test that the 'Default' option for the Default Form setting in the Plugin Settings works when
	 * creating and viewing a new WordPress Page, and there is no Default Form specified in the Plugin
	 * settings.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingDefaultFormWithNoDefaultFormSpecifiedInPlugin(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin with no default Forms configured.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: Default: None');

		// Check the order of the Form resources are alphabetical, with the Default and None options prepending the Forms.
		$I->checkSelectFormOptionOrder(
			$I,
			'#wp-convertkit-form',
			[
				'Default',
				'None',
			]
		);

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'Default' ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that no ConvertKit Form is displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test that the Default Form specified in the Plugin Settings works when
	 * creating and viewing a new WordPress Page.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingDefaultForm(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: Default');

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'Default' ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
	}

	/**
	 * Test that the Default Legacy Form specified in the Plugin Settings works when
	 * creating and viewing a new WordPress Page.
	 *
	 * @since   1.9.6.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingDefaultLegacyForm(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin to use legacy Form as default for Pages.
		$I->setupConvertKitPlugin(
			$I,
			[
				'page_form'    => $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'],
				'post_form'    => '',
				'product_form' => '',
			]
		);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: Legacy: Default');

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'Default' ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the ConvertKit Default Legacy Form displays.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . '/subscribe" data-remote="true">');

		// Confirm that the Legacy Form title's character encoding is correct.
		$I->seeInSource('Vantar þinn ungling sjálfstraust í stærðfræði?');
	}

	/**
	 * Test that 'None' Form specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingNoForm(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: None');

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', 'None' ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that no ConvertKit Form is displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test that the Form specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingDefinedForm(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
	}

	/**
	 * Test that the Modal Form is output once when the Autoptimize Plugin is active and
	 * its "Defer JavaScript" setting is enabled.
	 *
	 * @since   2.4.9
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingModalFormWithAutoptimizePlugin(AcceptanceTester $I)
	{
		// Setup Plugin and Resources.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Activate Autoptimize Plugin.
		$I->activateThirdPartyPlugin($I, 'autoptimize');

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] . ': Autoptimize');

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form,
		// and that Autoptimize hasn't moved the script embed to the footer of the site.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]', 1);

		// Deactivate Autoptimize Plugin.
		$I->deactivateThirdPartyPlugin($I, 'autoptimize');
	}

	/**
	 * Test that the Modal Form is output once when the Jetpack Boost Plugin is active and
	 * its "Defer Non-Essential JavaScript" setting is enabled.
	 *
	 * @since   2.4.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingModalFormWithJetpackBoostPlugin(AcceptanceTester $I)
	{
		// Setup Plugin and Resources.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Activate Jetpack Boost Plugin.
		$I->activateThirdPartyPlugin($I, 'jetpack-boost');

		// Enable Jetpack Boost's "Defer Non-Essential JavaScript" setting.
		$I->amOnAdminPage('admin.php?page=jetpack-boost');
		$I->click('#inspector-toggle-control-1');

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] . ': Jetpack Boost');

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form,
		// and that Jetpack Boost hasn't moved the script embed to the footer of the site.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]', 1);

		// Deactivate Jetpack Boost Plugin.
		$I->deactivateThirdPartyPlugin($I, 'jetpack-boost');
	}

	/**
	 * Test that the Modal Form is output once when the LiteSpeed Cache Plugin is active and
	 * its "Load JS Deferred" setting is enabled.
	 *
	 * @since   2.4.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingModalFormWithLiteSpeedCachePlugin(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Activate and enable LiteSpeed Cache Plugin.
		$I->activateThirdPartyPlugin($I, 'litespeed-cache');
		$I->enableCachingLiteSpeedCachePlugin($I);

		// Enable LiteSpeed Cache's "Load JS Deferred" setting.
		$I->amOnAdminPage('admin.php?page=litespeed-page_optm#settings_js');
		$I->click('label[for=input_radio_optmjs_defer_1]');
		$I->click('Save Changes');

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] . ': LiteSpeed Cache');

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form,
		// and that LiteSpeed Cache hasn't moved the script embed to the footer of the site.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]', 1);

		// Deactivate LiteSpeed Cache Plugin.
		$I->deactivateThirdPartyPlugin($I, 'litespeed-cache');
	}

	/**
	 * Test that the Modal Form <script> embed is output once when the Siteground Speed Optimizer Plugin is active
	 * and its "Combine JavaScript Files" setting is enabled.
	 *
	 * @since   2.4.5
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingModalFormWithSitegroundSpeedOptimizerPlugin(AcceptanceTester $I)
	{
		// Setup Plugin and Resources.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Activate Siteground Speed Optimizer Plugin.
		$I->activateThirdPartyPlugin($I, 'sg-cachepress');

		// Enable Siteground Speed Optimizer's "Combine JavaScript Files" setting.
		$I->haveOptionInDatabase('siteground_optimizer_combine_javascript', '1');

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] . ': Siteground Speed Optimizer');

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]', 1);

		// Deactivate Siteground Speed Optimizer Plugin.
		$I->deactivateThirdPartyPlugin($I, 'sg-cachepress');
	}

	/**
	 * Test that the Modal Form is output once when the Perfmatters Plugin is active and its "Delay JavaScript"
	 * setting is enabled.
	 *
	 * @since   2.4.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingModalFormWithPerfmattersPlugin(AcceptanceTester $I)
	{
		// Setup Plugin and Resources.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Activate Perfmatters Plugin.
		$I->activateThirdPartyPlugin($I, 'perfmatters');

		// Enable Defer and Delay JavaScript.
		$I->haveOptionInDatabase(
			'perfmatters_options',
			[
				'assets' => [
					'defer_js'            => 1,
					'delay_js'            => 1,
					'delay_js_inclusions' => '',
				],
			]
		);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] . ': WP Rocket');

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that one ConvertKit Form is output in the DOM within the <main> element.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]', 1);

		// Deactivate Perfmatters Plugin.
		$I->deactivateThirdPartyPlugin($I, 'perfmatters');
	}

	/**
	 * Test that the Modal Form is output once when the WP Rocket Plugin is active and its "Delay JavaScript execution"
	 * setting is enabled.
	 *
	 * @since   2.4.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingModalFormWithWPRocketPlugin(AcceptanceTester $I)
	{
		// Setup Plugin and Resources.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Activate WP Rocket Plugin.
		$I->activateThirdPartyPlugin($I, 'wp-rocket');

		// Load WP Rocket settings screen.
		$I->amOnAdminPage('options-general.php?page=wprocket#file_optimization');

		// Enable Delay JavaScript execution.
		$I->click('label[for=delay_js]');
		$I->waitForElementVisible('.wpr-isOpen');

		// Click Save Changes button.
		$I->click('Save Changes');

		// Confirm changes saved successfully.
		$I->waitForElementVisible('#setting-error-settings_updated');

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] . ': WP Rocket');

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_NAME'] ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that one ConvertKit Form is output in the DOM within the <main> element.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_FORMAT_MODAL_ID'] . '"]', 1);

		// Deactivate WP Rocket Plugin.
		$I->deactivateThirdPartyPlugin($I, 'wp-rocket');
	}

	/**
	 * Test that the Legacy Form specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 *
	 * @since   1.9.6.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingDefinedLegacyForm(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'form' => [ 'select2', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME'] ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the ConvertKit Legacy Form displays.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . '/subscribe" data-remote="true">');

		// Confirm that the Legacy Form title's character encoding is correct.
		$I->seeInSource('Vantar þinn ungling sjálfstraust í stærðfræði?');
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
	 * @since   1.9.7.2
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingInvalidDefinedForm(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Create Page, with an invalid Form ID, as if it were created prior to API credentials being changed and/or
		// a Form being deleted in ConvertKit.
		$pageID = $I->havePostInDatabase(
			[
				'post_type'  => 'page',
				'post_title' => 'ConvertKit: Page: Form: Specific: Invalid',
				'meta_input' => [
					'_wp_convertkit_post_meta' => [
						'form'         => '11111',
						'landing_page' => '',
						'tag'          => '',
					],
				],
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the invalid ConvertKit Form does not display.
		$I->dontSeeElementInDOM('form[data-sv-form="11111"]');

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
	}

	/**
	 * Test that the Default Form for Pages displays when the Default option is chosen via
	 * WordPress' Quick Edit functionality.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testQuickEditUsingDefaultForm(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Programmatically create a Page.
		$pageID = $I->havePostInDatabase(
			[
				'post_type'  => 'page',
				'post_title' => 'ConvertKit: Page: Form: Default: Quick Edit',
			]
		);

		// Quick Edit the Page in the Pages WP_List_Table.
		$I->quickEdit(
			$I,
			'page',
			$pageID,
			[
				'form' => [ 'select', 'Default' ],
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
	}

	/**
	 * Test that the defined form displays when chosen via
	 * WordPress' Quick Edit functionality.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testQuickEditUsingDefinedForm(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Programmatically create a Page.
		$pageID = $I->havePostInDatabase(
			[
				'post_type'  => 'page',
				'post_title' => 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Quick Edit',
			]
		);

		// Quick Edit the Page in the Pages WP_List_Table.
		$I->quickEdit(
			$I,
			'page',
			$pageID,
			[
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
	}

	/**
	 * Test that the Default Form for Pages displays when the Default option is chosen via
	 * WordPress' Bulk Edit functionality.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBulkEditUsingDefaultForm(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Programmatically create two Pages.
		$pageIDs = array(
			$I->havePostInDatabase(
				[
					'post_type'  => 'page',
					'post_title' => 'ConvertKit: Page: Form: Default: Bulk Edit #1',
				]
			),
			$I->havePostInDatabase(
				[
					'post_type'  => 'page',
					'post_title' => 'ConvertKit: Page: Form: Default: Bulk Edit #2',
				]
			),
		);

		// Bulk Edit the Pages in the Pages WP_List_Table.
		$I->bulkEdit(
			$I,
			'page',
			$pageIDs,
			[
				'form' => [ 'select', 'Default' ],
			]
		);

		// Iterate through Pages to run frontend tests.
		foreach ($pageIDs as $pageID) {
			// Load Page on the frontend site.
			$I->amOnPage('/?p=' . $pageID);

			// Check that no PHP warnings or notices were output.
			$I->checkNoWarningsAndNoticesOnScreen($I);

			// Confirm that one ConvertKit Form is output in the DOM.
			// This confirms that there is only one script on the page for this form, which renders the form.
			$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
		}
	}

	/**
	 * Test that the defined form displays when chosen via
	 * WordPress' Bulk Edit functionality.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBulkEditUsingDefinedForm(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Programmatically create two Pages.
		$pageIDs = array(
			$I->havePostInDatabase(
				[
					'post_type'  => 'page',
					'post_title' => 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Bulk Edit #1',
				]
			),
			$I->havePostInDatabase(
				[
					'post_type'  => 'page',
					'post_title' => 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Bulk Edit #2',
				]
			),
		);

		// Bulk Edit the Pages in the Pages WP_List_Table.
		$I->bulkEdit(
			$I,
			'page',
			$pageIDs,
			[
				'form' => [ 'select', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
			]
		);

		// Iterate through Pages to run frontend tests.
		foreach ($pageIDs as $pageID) {
			// Load Page on the frontend site.
			$I->amOnPage('/?p=' . $pageID);

			// Check that no PHP warnings or notices were output.
			$I->checkNoWarningsAndNoticesOnScreen($I);

			// Confirm that one ConvertKit Form is output in the DOM.
			// This confirms that there is only one script on the page for this form, which renders the form.
			$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
		}
	}

	/**
	 * Test that the existing settings are honored and not changed
	 * when the Bulk Edit options are set to 'No Change'.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBulkEditWithNoChanges(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Programmatically create two Pages with a defined form.
		$pageIDs = array(
			$I->havePostInDatabase(
				[
					'post_type'  => 'page',
					'post_title' => 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Bulk Edit with No Change #1',
					'meta_input' => [
						'_wp_convertkit_post_meta' => [
							'form'         => $_ENV['CONVERTKIT_API_FORM_ID'],
							'landing_page' => '',
							'tag'          => '',
						],
					],
				]
			),
			$I->havePostInDatabase(
				[
					'post_type'  => 'page',
					'post_title' => 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Bulk Edit with No Change #2',
					'meta_input' => [
						'_wp_convertkit_post_meta' => [
							'form'         => $_ENV['CONVERTKIT_API_FORM_ID'],
							'landing_page' => '',
							'tag'          => '',
						],
					],
				]
			),
		);

		// Bulk Edit the Pages in the Pages WP_List_Table.
		$I->bulkEdit(
			$I,
			'page',
			$pageIDs,
			[
				'form' => [ 'select', '— No Change —' ],
			]
		);

		// Iterate through Pages to run frontend tests.
		foreach ($pageIDs as $pageID) {
			// Load Page on the frontend site.
			$I->amOnPage('/?p=' . $pageID);

			// Check that no PHP warnings or notices were output.
			$I->checkNoWarningsAndNoticesOnScreen($I);

			// Confirm that one ConvertKit Form is output in the DOM.
			// This confirms that there is only one script on the page for this form, which renders the form.
			$I->seeNumberOfElementsInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]', 1);
		}
	}

	/**
	 * Test that the Bulk Edit fields do not display when a search on a WP_List_Table
	 * returns no results.
	 *
	 * @since   1.9.8.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testBulkEditFieldsHiddenWhenNoPagesFound(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Emulate the user searching for Pages with a query string that yields no results.
		$I->amOnAdminPage('edit.php?post_type=page&s=nothing');

		// Confirm that the Bulk Edit fields do not display.
		$I->dontSeeElement('#convertkit-bulk-edit');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
