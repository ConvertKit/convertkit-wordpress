<?php
/**
 * Tests for ConvertKit Landing Pages on WordPress Pages.
 *
 * @since   1.9.6
 */
class PageLandingPageCest
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
		// Activate and Setup ConvertKit plugin.
		$I->activateConvertKitPlugin($I);

		// Setup ConvertKit Plugin with no default form specified.
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);
	}

	/**
	 * Test that 'None' Landing Page specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingNoLandingPage(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Landing Page: None');

		// Check the order of the Landing Page resources are alphabetical, with the None option prepending the Landing Pages.
		$I->checkSelectLandingPageOptionOrder(
			$I,
			'#wp-convertkit-landing_page',
			[
				'None',
			]
		);

		// Configure metabox's Landing Page setting = None.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'landing_page' => [ 'select2', 'None' ],
			]
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that no ConvertKit Landing Page is displayed.
		$I->dontSeeElementInDOM('form[data-sv-form]');
	}

	/**
	 * Test that the Landing Page specified in the Page Settings works when
	 * creating and viewing a new WordPress Page, and that the Landing Page's
	 * "Redirect to an external page" setting in ConvertKit is honored.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingDefinedLandingPage(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Landing Page: ' . $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME']);

		// Configure metabox's Landing Page setting to value specified in the .env file.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'landing_page' => [ 'select2', $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME'] ],
			]
		);

		// Get Landing Page ID.
		$landingPageID = $I->grabValueFrom('#wp-convertkit-landing_page');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the basic HTML structure is correct.
		$I->seeLandingPageOutput($I, true);

		// Confirm the ConvertKit Site Icon displays.
		$I->seeInSource('<link rel="shortcut icon" type="image/x-icon" href="https://pages.convertkit.com/templates/favicon.ico">');

		// Confirm that the ConvertKit Landing Page displays.
		$I->dontSeeElementInDOM('body.page'); // WordPress didn't load its template, which is correct.
		$I->seeElementInDOM('form[data-sv-form="' . $landingPageID . '"]'); // ConvertKit injected its Landing Page Form, which is correct.

		// Subscribe.
		$I->fillField('email_address', $I->generateEmailAddress());
		$I->click('button.formkit-submit');

		// Confirm the Landing Page's redirect worked i.e. rocket-loader.min.js was not included and blocking, and the Landing Page
		// redirected to its external URL, https://cheerful-architect-3237.ck.page/.
		$I->waitForElementVisible('.creator-avatar');
	}

	/**
	 * Test that the WordPress site icon is output as the favicon on a Landing Page,
	 * when defined.
	 *
	 * @since   2.3.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testLandingPageSiteIcon(AcceptanceTester $I)
	{
		// Define a WordPress Site Icon.
		$imageID = $I->haveAttachmentInDatabase(codecept_data_dir('icon.png'));
		$I->haveOptionInDatabase('site_icon', $imageID);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Landing Page: Site Icon: ' . $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME']);

		// Configure metabox's Landing Page setting to value specified in the .env file.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'landing_page' => [ 'select2', $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME'] ],
			]
		);

		// Get Landing Page ID.
		$landingPageID = $I->grabValueFrom('#wp-convertkit-landing_page');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the basic HTML structure is correct.
		$I->seeLandingPageOutput($I, true);

		// Confirm the WordPress Site Icon displays.
		$I->seeInSource('<link rel="icon" href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/uploads/' . date( 'Y' ) . '/' . date( 'm' ) . '/icon-150x150.png" sizes="32x32">');
		$I->seeInSource('<link rel="icon" href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/uploads/' . date( 'Y' ) . '/' . date( 'm' ) . '/icon-300x300.png" sizes="192x192">');
		$I->seeInSource('<link rel="apple-touch-icon" href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/uploads/' . date( 'Y' ) . '/' . date( 'm' ) . '/icon-300x300.png">');
		$I->seeInSource('<meta name="msapplication-TileImage" content="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/uploads/' . date( 'Y' ) . '/' . date( 'm' ) . '/icon-300x300.png">');
		$I->dontSeeInSource('<link rel="shortcut icon" type="image/x-icon" href="https://pages.convertkit.com/templates/favicon.ico">');

		// Confirm that the ConvertKit Landing Page displays.
		$I->dontSeeElementInDOM('body.page'); // WordPress didn't load its template, which is correct.
		$I->seeElementInDOM('form[data-sv-form="' . $landingPageID . '"]'); // ConvertKit injected its Landing Page Form, which is correct.
	}

	/**
	 * Test that character encoding is correct when a Landing Page is output.
	 *
	 * @since   1.9.6.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testLandingPageCharacterEncoding(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Landing Page: ' . $_ENV['CONVERTKIT_API_LANDING_PAGE_CHARACTER_ENCODING_NAME']);

		// Configure metabox's Landing Page setting to value specified in the .env file.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'landing_page' => [ 'select2', $_ENV['CONVERTKIT_API_LANDING_PAGE_CHARACTER_ENCODING_NAME'] ],
			]
		);

		// Get Landing Page ID.
		$landingPageID = $I->grabValueFrom('#wp-convertkit-landing_page');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the basic HTML structure is correct.
		$I->seeLandingPageOutput($I);

		// Confirm that the Landing Page title is the same as defined on ConvertKit i.e. that character encoding is correct.
		$I->seeInSource('Vantar þinn ungling sjálfstraust í stærðfræði?');
	}

	/**
	 * Test that the Legacy Landing Page specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 *
	 * @since   1.9.6.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingDefinedLegacyLandingPage(AcceptanceTester $I)
	{
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Landing Page: ' . $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_NAME']);

		// Configure metabox's Landing Page setting to value specified in the .env file.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'landing_page' => [ 'select2', $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_NAME'] ],
			]
		);

		// Get Landing Page ID.
		$landingPageID = $I->grabValueFrom('#wp-convertkit-landing_page');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the basic HTML structure is correct.
		$I->seeLandingPageOutput($I);

		// Confirm that the ConvertKit Landing Page displays.
		$I->dontSeeElementInDOM('body.page'); // WordPress didn't load its template, which is correct.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://app.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_ID'] . '/subscribe" data-remote="true">'); // ConvertKit injected its Landing Page Form, which is correct.
	}

	/**
	 * Test that the WordPress site icon is output as the favicon on a Legacy Landing Page,
	 * when defined.
	 *
	 * @since   2.3.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testLegacyLandingPageSiteIcon(AcceptanceTester $I)
	{
		// Define a WordPress Site Icon.
		$imageID = $I->haveAttachmentInDatabase(codecept_data_dir('icon.png'));
		$I->haveOptionInDatabase('site_icon', $imageID);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Legacy Landing Page: Site Icon: ' . $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_NAME']);

		// Configure metabox's Landing Page setting to value specified in the .env file.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'landing_page' => [ 'select2', $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_NAME'] ],
			]
		);

		// Get Landing Page ID.
		$landingPageID = $I->grabValueFrom('#wp-convertkit-landing_page');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the basic HTML structure is correct.
		$I->seeLandingPageOutput($I);

		// Confirm the WordPress Site Icon displays.
		$I->seeInSource('<link rel="icon" href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/uploads/' . date( 'Y' ) . '/' . date( 'm' ) . '/icon-150x150.png" sizes="32x32">');
		$I->seeInSource('<link rel="icon" href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/uploads/' . date( 'Y' ) . '/' . date( 'm' ) . '/icon-300x300.png" sizes="192x192">');
		$I->seeInSource('<link rel="apple-touch-icon" href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/uploads/' . date( 'Y' ) . '/' . date( 'm' ) . '/icon-300x300.png">');
		$I->seeInSource('<meta name="msapplication-TileImage" content="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/uploads/' . date( 'Y' ) . '/' . date( 'm' ) . '/icon-300x300.png">');
		$I->dontSeeInSource('<link rel="shortcut icon" type="image/x-icon" href="https://pages.convertkit.com/templates/favicon.ico">');

		// Confirm that the ConvertKit Landing Page displays.
		$I->dontSeeElementInDOM('body.page'); // WordPress didn't load its template, which is correct.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://app.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_ID'] . '/subscribe" data-remote="true">'); // ConvertKit injected its Landing Page Form, which is correct.
	}

	/**
	 * Test that the Legacy Landing Page specified in the Page Settings works when
	 * the Landing Page was defined by the ConvertKit Plugin < 1.9.6, which used a URL
	 * instead of an ID.
	 *
	 * @since   1.9.6.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingDefinedLegacyLandingPageURL(AcceptanceTester $I)
	{
		// Create a Page with Plugin settings that contain a Legacy Landing Page URL,
		// mirroring how < 1.9.6 of the Plugin worked.
		$pageID = $I->havePageInDatabase(
			[
				'post_type'   => 'page',
				'post_status' => 'publish',
				'post_title'  => 'ConvertKit: Landing Page: Legacy URL',
				'post_name'   => 'convertkit-landing-page-legacy-url',
				'meta_input'  => [
					'_wp_convertkit_post_meta' => [
						'form'         => '0',
						// Emulates how Legacy Landing Pages were stored in < 1.9.6 as a URL, instead of an ID.
						'landing_page' => $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_URL'],
						'tag'          => '',
					],
				],
			]
		);

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-landing-page-legacy-url');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the basic HTML structure is correct.
		$I->seeLandingPageOutput($I);

		// Confirm that the ConvertKit Landing Page displays.
		$I->dontSeeElementInDOM('body.page'); // WordPress didn't load its template, which is correct.
		$I->seeInSource('<form id="ck_subscribe_form" class="ck_subscribe_form" action="https://app.convertkit.com/landing_pages/' . $_ENV['CONVERTKIT_API_LEGACY_LANDING_PAGE_ID'] . '/subscribe" data-remote="true">'); // ConvertKit injected its Landing Page Form, which is correct.
	}

	/**
	 * Test that the Landing Page specified in the Page Settings works when
	 * creating and viewing a new WordPress Page, with Perfmatters active.
	 *
	 * @since   2.5.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingDefinedLandingPageWithPerfmattersPlugin(AcceptanceTester $I)
	{
		// Setup Plugin and Resources.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Activate Perfmatters Plugin.
		$I->activateThirdPartyPlugin($I, 'perfmatters');

		// Enable Lazy Loading.
		$I->haveOptionInDatabase(
			'perfmatters_options',
			[
				'lazyload' => [
					'lazy_loading' => 1,
				],
			]
		);

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Landing Page: Perfmatters: ' . $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME']);

		// Configure metabox's Landing Page setting to value specified in the .env file.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'landing_page' => [ 'select2', $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME'] ],
			]
		);

		// Get Landing Page ID.
		$landingPageID = $I->grabValueFrom('#wp-convertkit-landing_page');

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage($I);

		// Confirm that the basic HTML structure is correct.
		$I->seeLandingPageOutput($I, true);

		// Confirm the ConvertKit Site Icon displays.
		$I->seeInSource('<link rel="shortcut icon" type="image/x-icon" href="https://pages.convertkit.com/templates/favicon.ico">');

		// Confirm that the ConvertKit Landing Page displays.
		$I->dontSeeElementInDOM('body.page'); // WordPress didn't load its template, which is correct.
		$I->seeElementInDOM('form[data-sv-form="' . $landingPageID . '"]'); // ConvertKit injected its Landing Page Form, which is correct.

		// Confirm that Perfmatters has not lazy loaded assets.
		$I->dontSeeElementInDOM('.perfmatters-lazy');

		// Deactivate Perfmatters Plugin.
		$I->deactivateThirdPartyPlugin($I, 'perfmatters');
	}

	/**
	 * Test that the Landing Page specified in the Page Settings works when
	 * creating and viewing a new WordPress Page, with the WP-Rocket caching
	 * and minification Plugin active.
	 *
	 * @since   2.4.4
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPageUsingDefinedLandingPageWithWPRocket(AcceptanceTester $I)
	{
		// Activate WP Rocket Plugin.
		$I->activateThirdPartyPlugin($I, 'wp-rocket');

		// Load WP Rocket settings screen.
		$I->amOnAdminPage('options-general.php?page=wprocket#file_optimization');

		// Enable CSS minification.
		$I->click('label[for=minify_css]');
		$I->waitForElementVisible('.wpr-isOpen');
		$I->click('Activate minify CSS');

		// Enable JS minification.
		$I->click('label[for=minify_js]');
		$I->waitForElementVisible('.wpr-isOpen');
		$I->click('Activate minify JavaScript');

		// Enable image lazy loading.
		$I->click('#wpr-nav-media');
		$I->click('label[for=lazyload]');
		$I->click('label[for=lazyload_css_bg_img]');

		// Click Save Changes button.
		$I->click('Save Changes');

		// Confirm changes saved successfully.
		$I->waitForElementVisible('#setting-error-settings_updated');

		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage($I, 'page', 'ConvertKit: Page: Landing Page: WP Rocket: ' . $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME']);

		// Configure metabox's Landing Page setting to value specified in the .env file.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			[
				'landing_page' => [ 'select2', $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME'] ],
			]
		);

		// Get Landing Page ID.
		$landingPageID = $I->grabValueFrom('#wp-convertkit-landing_page');

		// Publish and view the Page on the frontend site.
		$url = $I->publishAndViewGutenbergPage($I);

		// Log out, as WP Rocket won't cache or minify for logged in WordPress Users.
		$I->logOut();

		// View the Page.
		$I->amOnUrl($url);

		// Confirm that the basic HTML structure is correct.
		$I->seeLandingPageOutput($I, true);

		// Confirm the ConvertKit Site Icon displays.
		$I->seeInSource('<link rel="shortcut icon" type="image/x-icon" href="https://pages.convertkit.com/templates/favicon.ico">');

		// Confirm that the ConvertKit Landing Page displays.
		$I->dontSeeElementInDOM('body.page'); // WordPress didn't load its template, which is correct.
		$I->seeElementInDOM('form[data-sv-form="' . $landingPageID . '"]'); // ConvertKit injected its Landing Page Form, which is correct.

		// Confirm that WP Rocket has not minified any CSS or JS assets.
		$I->dontSeeElementInDOM('script[data-minify="1"]');

		// Confirm that WP Rocket has not attempted to lazy load images.
		$I->dontSeeElementInDOM('.rocket-lazyload');

		// Deactivate WP Rocket Plugin.
		$I->deactivateThirdPartyPlugin($I, 'wp-rocket');
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
