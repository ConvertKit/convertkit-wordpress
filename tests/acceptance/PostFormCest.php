<?php
/**
 * Tests for ConvertKit Forms on WordPress Posts.
 * 
 * @since 	1.9.6
 */
class PostFormCest
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
	 * creating and viewing a new WordPress Post, and there is no Default Form specified in the Plugin
	 * settings.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPostUsingDefaultFormWithNoDefaultFormSpecifiedInPlugin(AcceptanceTester $I)
	{
		// Add a Post using the Gutenberg editor.
		$I->addGutenbergPage($I, 'post', 'ConvertKit: Post: Form: Default: None');

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', 'Default' ],
		]);

		// Publish and view the Post on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that no ConvertKit Form is displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test that the Default Form specified in the Plugin Settings works when
	 * creating and viewing a new WordPress Post.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPostUsingDefaultForm(AcceptanceTester $I)
	{
		// Specify the Default Form in the Plugin Settings.
		$defaultFormID = $I->setupConvertKitPluginDefaultForm($I);

		// Add a Post using the Gutenberg editor.
		$I->addGutenbergPage($I, 'post', 'ConvertKit: Post: Form: Default');

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', 'Default' ],
		]);

		// Publish and view the Post on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the ConvertKit Default Form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $defaultFormID . '"]');
	}

	/**
	 * Test that the Default Legacy Form specified in the Plugin Settings works when
	 * creating and viewing a new WordPress Post.
	 * 
	 * @since 	1.9.6.3
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPostUsingDefaultLegacyForm(AcceptanceTester $I)
	{
		// Specify the Default Legacy Form in the Plugin Settings.
		$defaultLegacyFormID = $I->setupConvertKitPluginDefaultLegacyForm($I);

		// Add a Post using the Gutenberg editor.
		$I->addGutenbergPage($I, 'post', 'ConvertKit: Post: Form: Legacy: Default');

		// Configure metabox's Form setting = Default.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', 'Default' ],
		]);

		// Publish and view the Post on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the ConvertKit Default Legacy Form displays.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $defaultLegacyFormID . '/subscribe" data-remote="true">');
	}

	/**
	 * Test that 'None' Form specified in the Post Settings works when
	 * creating and viewing a new WordPress Post.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPostUsingNoForm(AcceptanceTester $I)
	{
		// Add a Post using the Gutenberg editor.
		$I->addGutenbergPage($I, 'post', 'ConvertKit: Post: Form: None');

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', 'None' ],
		]);

		// Publish and view the Post on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that no ConvertKit Form is displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test that the Form specified in the Post Settings works when
	 * creating and viewing a new WordPress Post.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPostUsingDefinedForm(AcceptanceTester $I)
	{
		// Add a Post using the Gutenberg editor.
		$I->addGutenbergPage($I, 'post', 'ConvertKit: Post: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', $_ENV['CONVERTKIT_API_FORM_NAME'] ],
		]);

		// Get Form ID.
		$formID = $I->grabValueFrom('#wp-convertkit-form');

		// Publish and view the Post on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the ConvertKit Form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $formID . '"]');
	}

	/**
	 * Test that the Legacy Form specified in the Post Settings works when
	 * creating and viewing a new WordPress Post.
	 * 
	 * @since 	1.9.6.3
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPostUsingDefinedLegacyForm(AcceptanceTester $I)
	{
		// Add a Post using the Gutenberg editor.
		$I->addGutenbergPage($I, 'post', 'ConvertKit: Post: Form: ' . $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME']);

		// Configure metabox's Form setting = None.
		$I->configureMetaboxSettings($I, 'wp-convertkit-meta-box', [
			'form' => [ 'select2', $_ENV['CONVERTKIT_API_LEGACY_FORM_NAME'] ],
		]);

		// Get Form ID.
		$formID = $I->grabValueFrom('#wp-convertkit-form');

		// Publish and view the Post on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the ConvertKit Legacy Form displays.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://api.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_FORM_ID'] . '/subscribe" data-remote="true">');
	}

	/**
	 * Test that the Default Form for Posts displays when an invalid Form ID is specified
	 * for a Post.
	 * 
	 * Whilst the on screen options won't permit selecting an invalid Form ID, a Post might
	 * have an invalid Form ID because:
	 * - the form belongs to another ConvertKit account (i.e. API credentials were changed in the Plugin, but this Post's specified Form was not changed)
	 * - the form was deleted from the ConvertKit account.
	 * 
	 * @since 	1.9.7.2
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPostUsingInvalidDefinedForm(AcceptanceTester $I)
	{
		// Setup the Default Form for Pages and Posts.
		$I->setupConvertKitPluginDefaultForm($I);

		// Create Post, with an invalid Form ID, as if it were created prior to API credentials being changed and/or
		// a Form being deleted in ConvertKit.
		$postID = $I->havePostInDatabase([
			'post_type' 	=> 'post',
			'post_title' 	=> 'ConvertKit: Post: Form: Specific: Invalid',
			'meta_input'	=> [
				'_wp_convertkit_post_meta' => [
					'form'         => '11111',
					'landing_page' => '',
					'tag'          => '',
				]
			],
		]);

		// Load the Post on the frontend site.
		$I->amOnPage('/?p='.$postID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the invalid ConvertKit Form does not display.
		$I->dontSeeElementInDOM('form[data-sv-form="11111"]');

		// Confirm that the Default Form for Posts does display as a fallback.
		$I->seeElementInDOM('form[data-sv-form="' . $_ENV['CONVERTKIT_API_FORM_ID'] . '"]');
	}

	/**
	 * Test that the Form specified in the Category assigned to the WordPress Post is used when the WordPress Post
	 * is set to use the Default Form.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPostUsingDefaultFormWithCategoryFormSpecified(AcceptanceTester $I)
	{
		// Create Category.
		$termID = $I->haveTermInDatabase( 'ConvertKit', 'category' );
		$termID = $termID[0];
		
		// Create Post, assigned to ConvertKit Category.
		$postID = $I->havePostInDatabase([
			'post_type' 	=> 'post',
			'post_title' 	=> 'ConvertKit Form inherited from ConvertKit Category',
			'tax_input' => [
				[ 'category' => $termID ],
			],
		]);

		// Edit the Term, defining a Form.
		$I->amOnAdminPage('term.php?taxonomy=category&tag_ID=' . $termID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-form');

		// Change Form to value specified in the .env file.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Click Update
		$I->click('Update');

		// Check that the update succeeded.
		$I->seeElementInDOM('div.notice-success');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Get Form ID.
		$formID = $I->grabValueFrom('#wp-convertkit-form');

		// Load the Post on the frontend site
		$I->amOnPage('/?p=' . $postID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $formID . '"]');
	}

  	/**
	 * Test that the Default Form specified at Plugin level for Posts displays when:
	 * - A Category was created with ConvertKit Form = 'None' when 1.9.5.2 or earlier of the Plugin was activated,
	 * - The WordPress Post is set to use the Default Form,
	 * - A Default Form is set in the Plugin settings.
	 * 
	 * 1.9.5.2 and earlier stored the 'None' option as 'default' for Categories, meaning that the Post (or Plugin) default form
	 * should be used. 
	 * 
	 * 1.9.6.0 and later changed the value to 0 for Categories, bringing it in line with the Post Form's 'None'
	 * setting.
	 * 
	 * @since 	1.9.7.3
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPostUsingDefaultFormWithCategoryCreatedBefore1960(AcceptanceTester $I)
	{
		// Setup Default Forms.
		$I->setupConvertKitPluginDefaultForm($I);

		// Create Category as if it were created / edited when the ConvertKit Plugin < 1.9.6.0
		// was active.
		$termID = $I->haveTermInDatabase( 'ConvertKit 1.9.5.2 and earlier', 'category', [
			'meta' => [
				'ck_default_form' => 'default', // Emulate how 1.9.5.2 and earlier store this setting.
			],
		] );
		$termID = $termID[0];
		
		// Create Post, assigned to Category.
		$postID = $I->havePostInDatabase([
			'post_type' 	=> 'post',
			'post_title' 	=> 'ConvertKit: Default Form: Category Created before 1.9.6.0',
			'tax_input' => [
				[ 'category' => $termID ],
			],
		]);

		// Load the Post on the frontend site.
		$I->amOnPage('/?p=' . $postID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Form displays as defined in the Plugin Settings.
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