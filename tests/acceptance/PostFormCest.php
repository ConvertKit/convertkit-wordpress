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

		// Navigate to Post > Add New
		$I->amOnAdminPage('post-new.php');

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed
		$I->maybeCloseGutenbergWelcomeModal($I);
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
		// Navigate to Post > Add New
		$I->amOnAdminPage('post-new.php');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-form');

		// Change Form to Default
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', 'Default');

		// Define a Post Title.
		$I->fillField('.editor-post-title__input', 'ConvertKit: Form: Default: None');

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

		// Load the Post on the frontend site.
		$I->amOnPage('/convertkit-form-default-none');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

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

		// Navigate to Post > Add New
		$I->amOnAdminPage('post-new.php');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-form');

		// Change Form to Default
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', 'Default');

		// Define a Post Title.
		$I->fillField('.editor-post-title__input', 'ConvertKit: Form: Default');

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

		// Load the Post on the frontend site
		$I->amOnPage('/convertkit-form-default');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Default Form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $defaultFormID . '"]');
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
		// Navigate to Posts > Add New
		$I->amOnAdminPage('post-new.php');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-form');

		// Change Form to 'None'
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', 'None');

		// Define a Post Title.
		$I->fillField('.editor-post-title__input', 'ConvertKit: Form: None');

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

		// Load the Post on the frontend site.
		$I->amOnPage('/convertkit-form-none');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

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
		// Navigate to Posts > Add New
		$I->amOnAdminPage('post-new.php');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-form');

		// Change Form to value specified in the .env file.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Define a Post Title.
		$I->fillField('.editor-post-title__input', 'ConvertKit: Form: Specific');

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
		$formID = $I->grabValueFrom('#wp-convertkit-form');

		// Load the Post on the frontend site
		$I->amOnPage('/convertkit-form-specific');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Form displays.
		$I->seeElementInDOM('form[data-sv-form="' . $formID . '"]');
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