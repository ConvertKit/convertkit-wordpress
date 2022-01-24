<?php
/**
 * Tests for ConvertKit Landing Pages on WordPress Pages.
 * 
 * @since 	1.9.6
 */
class PageLandingPageCest
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
	 * Test that 'None' Landing Page specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPageUsingNoLandingPage(AcceptanceTester $I)
	{
		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-landing_page');

		// Change Landing Page to 'None'
		$I->fillSelect2Field($I, '#select2-wp-convertkit-landing_page-container', 'None');

		// Define a Page Title.
		$I->fillField('#post-title-0', 'ConvertKit: Landing Page: None');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');

		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Check the value of the Landing Page field matches the input provided.
		$I->performOn( '.post-publish-panel__postpublish-buttons', function($I) {
			$I->seeOptionIsSelected('#wp-convertkit-landing_page', 'None');
		});

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-landing-page-none');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that no ConvertKit Landing Page is displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test that the Landing Page specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPageUsingDefinedLandingPage(AcceptanceTester $I)
	{
		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-landing_page');

		// Change Landing Page to value specified in the .env file.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-landing_page-container', $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME']);

		// Define a Page Title.
		$I->fillField('#post-title-0', 'ConvertKit: Landing Page: Specific');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Check the value of the Landing Page field matches the input provided.
		$I->performOn( '.post-publish-panel__postpublish-buttons', function($I) {
			$I->seeOptionIsSelected('#wp-convertkit-landing_page', $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME']);
		});

		// Get Landing Page ID.
		$landingPageID = $I->grabValueFrom('#wp-convertkit-landing_page');

		// Load the Page on the frontend site
		$I->amOnPage('/convertkit-landing-page-specific');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Landing Page displays.
		$I->dontSeeElementInDOM('body.page'); // WordPress didn't load its template, which is correct.
		$I->seeElementInDOM('form[data-sv-form="' . $landingPageID . '"]'); // ConvertKit injected its Landing Page Form, which is correct.
	}

	/**
	 * Test that character encoding is correct when a Landing Page is output.
	 * 
	 * @since 	1.9.6.1
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testLandingPageCharacterEncoding(AcceptanceTester $I)
	{
		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-landing_page');

		// Change Landing Page to value specified in the .env file.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-landing_page-container', $_ENV['CONVERTKIT_API_LANDING_PAGE_CHARACTER_ENCODING_NAME']);

		// Define a Page Title.
		$I->fillField('#post-title-0', 'ConvertKit: Landing Page: Character Encoding');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Check the value of the Landing Page field matches the input provided.
		$I->performOn( '.post-publish-panel__postpublish-buttons', function($I) {
			$I->seeOptionIsSelected('#wp-convertkit-landing_page', $_ENV['CONVERTKIT_API_LANDING_PAGE_CHARACTER_ENCODING_NAME']);
		});

		// Get Landing Page ID.
		$landingPageID = $I->grabValueFrom('#wp-convertkit-landing_page');

		// Load the Page on the frontend site
		$I->amOnPage('/convertkit-landing-page-character-encoding');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the Landing Page title is the same as defined on ConvertKit i.e. that character encoding is correct.
		$I->seeInSource('Vantar þinn ungling sjálfstraust í stærðfræði?');
	}

	/**
	 * Test that the Legacy Landing Page specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 * 
	 * @since 	1.9.6.3
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPageUsingDefinedLegacyLandingPage(AcceptanceTester $I)
	{
		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-landing_page');

		// Change Landing Page to value specified in the .env file.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-landing_page-container', $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_NAME']);

		// Define a Page Title.
		$I->fillField('#post-title-0', 'ConvertKit: Landing Page: Legacy: Specific');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Check the value of the Landing Page field matches the input provided.
		$I->performOn( '.post-publish-panel__postpublish-buttons', function($I) {
			$I->seeOptionIsSelected('#wp-convertkit-landing_page', $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_NAME']);
		});

		// Get Landing Page ID.
		$landingPageID = $I->grabValueFrom('#wp-convertkit-landing_page');

		// Load the Page on the frontend site
		$I->amOnPage('/convertkit-landing-page-legacy-specific');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Landing Page displays.
		$I->dontSeeElementInDOM('body.page'); // WordPress didn't load its template, which is correct.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://app.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_ID'] . '/subscribe" data-remote="true">'); // ConvertKit injected its Landing Page Form, which is correct.
	}

	/**
	 * Test that the Legacy Landing Page specified in the Page Settings works when
	 * the Landing Page was defined by the ConvertKit Plugin < 1.9.6, which used a URL
	 * instead of an ID.
	 * 
	 * @since 	1.9.6.3
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPageUsingDefinedLegacyLandingPageURL(AcceptanceTester $I)
	{
		// Create a Page with Plugin settings that contain a Legacy Landing Page URL,
		// mirroring how < 1.9.6 of the Plugin worked.
		$pageID = $I->havePageInDatabase([
			'post_type' => 'page',
			'post_status' => 'publish',
			'post_title' => 'ConvertKit: Landing Page: Legacy URL',
			'post_name' => 'convertkit-landing-page-legacy-url',
			'meta_input' => [
				'_wp_convertkit_post_meta' => [
					'form'         => '-1',
					// Emulates how Legacy Landing Pages were stored in < 1.9.6 as a URL, instead of an ID.
					'landing_page' => $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_URL'],
					'tag'          => '',
				],
			],
		]);

		// Load the Page on the frontend site
		$I->amOnPage('/convertkit-landing-page-legacy-url');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the ConvertKit Landing Page displays.
		$I->dontSeeElementInDOM('body.page'); // WordPress didn't load its template, which is correct.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://app.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_ID'] . '/subscribe" data-remote="true">'); // ConvertKit injected its Landing Page Form, which is correct.
	}
}