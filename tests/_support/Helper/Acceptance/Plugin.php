<?php
namespace Helper\Acceptance;

/**
 * Helper methods and actions related to the ConvertKit Plugin,
 * which are then available using $I->{yourFunctionName}.
 *
 * @since   1.9.6
 */
class Plugin extends \Codeception\Module
{
	/**
	 * Helper method to activate the ConvertKit Plugin, checking
	 * it activated and no errors were output.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function activateConvertKitPlugin($I)
	{
		$I->activateThirdPartyPlugin($I, 'convertkit');
	}

	/**
	 * Helper method to deactivate the ConvertKit Plugin, checking
	 * it activated and no errors were output.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function deactivateConvertKitPlugin($I)
	{
		$I->deactivateThirdPartyPlugin($I, 'convertkit');
	}

	/**
	 * Helper method to programmatically setup the Plugin's API Key and Secret,
	 * enabling debug logging.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I              AcceptanceTester.
	 * @param   bool|string      $apiKey         API Key (if specified, used instead of CONVERTKIT_API_KEY).
	 * @param   bool|string      $apiSecret      API Secret (if specified, used instead of CONVERTKIT_API_SECRET).
	 * @param   bool|string      $pageFormID     Default Form ID for Pages (if specified, used instead of CONVERTKIT_API_FORM_ID).
	 * @param   bool|string      $postFormID     Default Form ID for Posts (if specified, used instead of CONVERTKIT_API_FORM_ID).
	 * @param   bool|string      $productFormID  Default Form ID for Products (if specified, used instead of CONVERTKIT_API_FORM_ID).
	 */
	public function setupConvertKitPlugin($I, $apiKey = false, $apiSecret = false, $pageFormID = false, $postFormID = false, $productFormID = false)
	{
		// Define the API Key and Secret, with Debug Log enabled.
		$I->haveOptionInDatabase(
			'_wp_convertkit_settings',
			[
				'api_key'      => ( $apiKey !== false ? $apiKey : $_ENV['CONVERTKIT_API_KEY'] ),
				'api_secret'   => ( $apiSecret !== false ? $apiSecret : $_ENV['CONVERTKIT_API_SECRET'] ),
				'debug'        => 'on',
				'no_scripts'   => '',
				'no_css'       => '',
				'post_form'    => ( $postFormID !== false ? $postFormID : $_ENV['CONVERTKIT_API_FORM_ID'] ),
				'page_form'    => ( $pageFormID !== false ? $pageFormID : $_ENV['CONVERTKIT_API_FORM_ID'] ),
				'product_form' => ( $productFormID !== false ? $productFormID : $_ENV['CONVERTKIT_API_FORM_ID'] ),
			]
		);
	}

	/**
	 * Helper method to define cached Resources (Forms, Landing Pages, Posts, Products and Tags),
	 * directly into the database, instead of querying the API for them via the Resource classes.
	 *
	 * This can safely be done for Acceptance tests, as WPUnit tests ensure that
	 * caching Resources from calls made to the API work and store data in the expected
	 * structure.
	 *
	 * Defining cached Resources here reduces the number of API calls made for each test,
	 * reducing the likelihood of hitting a rate limit due to running tests in parallel.
	 *
	 * Resources are deliberately not in order, to emulate how the data might not always
	 * be in alphabetical / published order from the API.
	 *
	 * @since   2.0.7
	 *
	 * @param   AcceptanceTester $I              AcceptanceTester.
	 */
	public function setupConvertKitPluginResources($I)
	{
		// Define Forms as if the Forms resource class populated them from the API.
		$I->haveOptionInDatabase(
			'convertkit_forms',
			[
				3003590 => [
					'id'         => 3003590,
					'name'       => 'Third Party Integrations Form',
					'created_at' => '2022-02-17T15:05:31.000Z',
					'type'       => 'embed',
					'format'     => 'inline',
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/71cbcc4042/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/71cbcc4042',
					'archived'   => false,
					'uid'        => '71cbcc4042',
				],
				2780977 => [
					'id'         => 2780977,
					'name'       => 'Modal Form',
					'created_at' => '2021-11-17T04:22:06.000Z',
					'type'       => 'embed',
					'format'     => 'modal',
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/397e876257/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/397e876257',
					'archived'   => false,
					'uid'        => '397e876257',
				],
				2780979 => [
					'id'         => 2780979,
					'name'       => 'Slide In Form',
					'created_at' => '2021-11-17T04:22:24.000Z',
					'type'       => 'embed',
					'format'     => 'slide in',
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/e0d65bed9d/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/e0d65bed9d',
					'archived'   => false,
					'uid'        => 'e0d65bed9d',
				],
				2765139 => [
					'id'         => 2765139,
					'name'       => 'Page Form',
					'created_at' => '2021-11-11T15:30:40.000Z',
					'type'       => 'embed',
					'format'     => 'inline',
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/85629c512d/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/85629c512d',
					'archived'   => false,
					'uid'        => '85629c512d',
				],
				470099  => [
					'id'                  => 470099,
					'name'                => 'Legacy Form',
					'created_at'          => null,
					'type'                => 'embed',
					'url'                 => 'https://app.convertkit.com/landing_pages/470099',
					'embed_js'            => 'https://api.convertkit.com/api/v3/forms/470099.js?api_key=' . $_ENV['CONVERTKIT_API_KEY'],
					'embed_url'           => 'https://api.convertkit.com/api/v3/forms/470099.html?api_key=' . $_ENV['CONVERTKIT_API_KEY'],
					'title'               => 'Join the newsletter',
					'description'         => '<p>Subscribe to get our latest content by email.</p>',
					'sign_up_button_text' => 'Subscribe',
					'success_message'     => 'Success! Now check your email to confirm your subscription.',
					'archived'            => false,
				],
				2780980 => [
					'id'         => 2780980,
					'name'       => 'Sticky Bar Form',
					'created_at' => '2021-11-17T04:22:42.000Z',
					'type'       => 'embed',
					'format'     => 'sticky bar',
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/9f5c601482/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/9f5c601482',
					'archived'   => false,
					'uid'        => '9f5c601482',
				],
				3437554 => [
					'id'         => 3437554,
					'name'       => 'AAA Test',
					'created_at' => '2022-07-15T15:06:32.000Z',
					'type'       => 'embed',
					'format'     => 'inline',
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/3bb15822a2/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/3bb15822a2',
					'archived'   => false,
					'uid'        => '3bb15822a2',
				],
				2765149 => [
					'id'         => 2765149,
					'name'       => 'WooCommerce Product Form',
					'created_at' => '2021-11-11T15:32:54.000Z',
					'type'       => 'embed',
					'format'     => 'inline',
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/7e238f3920/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/7e238f3920',
					'archived'   => false,
					'uid'        => '7e238f3920',
				],
			]
		);

		// Define Landing Pages.
		$I->haveOptionInDatabase(
			'convertkit_landing_pages',
			[
				2765196 => [
					'id'         => 2765196,
					'name'       => 'Landing Page',
					'created_at' => '2021-11-11T15:45:33.000Z',
					'type'       => 'hosted',
					'format'     => null,
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/99f1db6843/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/99f1db6843',
					'archived'   => false,
					'uid'        => '99f1db6843',
				],
				2849151 => [
					'id'         => 2849151,
					'name'       => 'Character Encoding',
					'created_at' => '2021-12-16T14:55:58.000Z',
					'type'       => 'hosted',
					'format'     => null,
					'embed_js'   => 'https://cheerful-architect-3237.ck.page/cc5eb21744/index.js',
					'embed_url'  => 'https://cheerful-architect-3237.ck.page/cc5eb21744',
					'archived'   => false,
					'uid'        => 'cc5eb21744',
				],
				470103  => [
					'id'                  => 470103,
					'name'                => 'Legacy Landing Page',
					'created_at'          => null,
					'type'                => 'hosted',
					'url'                 => 'https://app.convertkit.com/landing_pages/470103',
					'embed_js'            => 'https://api.convertkit.com/api/v3/forms/470103.js?api_key=' . $_ENV['CONVERTKIT_API_KEY'],
					'embed_url'           => 'https://api.convertkit.com/api/v3/forms/470103.html?api_key=' . $_ENV['CONVERTKIT_API_KEY'],
					'title'               => '',
					'description'         => '',
					'sign_up_button_text' => 'Register',
					'success_message'     => null,
					'archived'            => false,
				],
			]
		);

		// Define Posts.
		$I->haveOptionInDatabase(
			'convertkit_posts',
			[
				224758  => [
					'id'            => 224758,
					'title'         => 'Test Subject',
					'url'           => 'https://cheerful-architect-3237.ck.page/posts/test-subject',
					'published_at'  => '2022-01-24T00:00:00.000Z',
					'description'   => 'Description text for Test Subject',
					'thumbnail_url' => 'https://placehold.co/600x400',
					'thumbnail_alt' => 'Alt text for Test Subject',
					'is_paid'       => null,
				],
				489480  => [
					'id'            => 489480,
					'title'         => 'Broadcast 2',
					'url'           => 'https://cheerful-architect-3237.ck.page/posts/broadcast-2',
					'published_at'  => '2022-04-08T00:00:00.000Z',
					'description'   => 'Description text for Broadcast 2',
					'thumbnail_url' => 'https://placehold.co/600x400',
					'thumbnail_alt' => 'Alt text for Broadcast 2',
					'is_paid'       => null,
				],
				3175837 => [
					'id'            => 3175837,
					'title'         => 'HTML Template Test',
					'url'           => 'https://cheerful-architect-3237.ck.page/posts/html-template-test',
					'published_at'  => '2023-08-02T16:34:51.000Z',
					'description'   => "Heading 1\nParagraph\nLorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi ultrices vehicula erat, eu faucibus ligula viverra sit amet. Nullam porta scelerisque lacus eu dignissim. Curabitur mattis dui est, sed gravida ex tincidunt sed.\nLorem ipsum dolor sit amet, consectetur adipiscing...",
					'thumbnail_url' => 'https://embed.filekitcdn.com/e/pX62TATVeCKK5QzkXWNLw3/qM63x7vF3qN1whboGdEpuL',
					'thumbnail_alt' => 'MacBook Pro beside plant in vase',
					'is_paid'       => true,
				],
				572575  => [
					'id'            => 572575,
					'title'         => 'Paid Subscriber Broadcast',
					'url'           => 'https://cheerful-architect-3237.ck.page/posts/paid-subscriber-broadcast',
					'published_at'  => '2022-05-03T00:00:00.000Z',
					'description'   => 'Description text for Paid Subscriber Broadcast',
					'thumbnail_url' => 'https://placehold.co/600x400',
					'thumbnail_alt' => 'Alt text for Paid Subscriber Broadcast',
					'is_paid'       => true,
				],
			]
		);

		// Define Products.
		$I->haveOptionInDatabase(
			'convertkit_products',
			[
				36377 => [
					'id'        => 36377,
					'name'      => 'Newsletter Subscription',
					'url'       => 'https://cheerful-architect-3237.ck.page/products/newsletter-subscription',
					'published' => true,
				],
			]
		);

		// Define Tags.
		$I->haveOptionInDatabase(
			'convertkit_tags',
			[
				2744672 => [
					'id'         => 2744672,
					'name'       => 'wordpress',
					'created_at' => '2021-11-11T19:30:06.000Z',
				],
				2907192 => [
					'id'         => 2907192,
					'name'       => 'gravityforms-tag-1',
					'created_at' => '2022-02-02T14:06:32.000Z',
				],
				3748541 => [
					'id'         => 3748541,
					'name'       => 'wpforms',
					'created_at' => '2023-03-29T12:32:38.000Z',
				],
				2907193 => [
					'id'         => 2907193,
					'name'       => 'gravityforms-tag-2',
					'created_at' => '2022-02-02T14:06:38.000Z',
				],
			]
		);

		// Define last queried to now for all resources, so they're not automatically immediately refreshed by the Plugin's logic.
		$I->haveOptionInDatabase( 'convertkit_forms_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_landing_pages_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_posts_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_products_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_tags_last_queried', strtotime( 'now' ) );
	}

	/**
	 * Helper method to define cached Resources (Forms, Landing Pages, Posts, Products and Tags),
	 * directly into the database, instead of querying the API for them via the Resource classes
	 * as if the ConvertKit account is new and has no resources defined in ConvertKit.
	 *
	 * @since   2.0.7
	 *
	 * @param   AcceptanceTester $I              AcceptanceTester.
	 */
	public function setupConvertKitPluginResourcesNoData($I)
	{
		// Define Forms as if the Forms resource class populated them from the API.
		$I->haveOptionInDatabase(
			'convertkit_forms',
			[]
		);

		// Define Landing Pages.
		$I->haveOptionInDatabase(
			'convertkit_landing_pages',
			[]
		);

		// Define Posts.
		$I->haveOptionInDatabase(
			'convertkit_posts',
			[]
		);

		// Define Products.
		$I->haveOptionInDatabase(
			'convertkit_products',
			[]
		);

		// Define Tags.
		$I->haveOptionInDatabase(
			'convertkit_tags',
			[]
		);

		// Define last queried to now for all resources, so they're not automatically immediately refreshed by the Plugin's logic.
		$I->haveOptionInDatabase( 'convertkit_forms_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_landing_pages_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_posts_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_products_last_queried', strtotime( 'now' ) );
		$I->haveOptionInDatabase( 'convertkit_tags_last_queried', strtotime( 'now' ) );
	}

	/**
	 * Helper method to setup the Plugin's Member Content settings.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I          AcceptanceTester.
	 * @param   bool|array       $settings   Array of key/value settings. If not defined, uses expected defaults.
	 */
	public function setupConvertKitPluginRestrictContent($I, $settings = false)
	{
		// Go to the Plugin's Member Content Screen.
		$I->loadConvertKitSettingsRestrictContentScreen($I);

		// Complete fields.
		if ( $settings ) {
			foreach ( $settings as $key => $value ) {
				switch ( $key ) {
					case 'enabled':
						if ( $value ) {
							$I->checkOption('_wp_convertkit_settings_restrict_content[' . $key . ']');
						} else {
							$I->uncheckOption('_wp_convertkit_settings_restrict_content[' . $key . ']');
						}
						break;
					default:
						$I->fillField('_wp_convertkit_settings_restrict_content[' . $key . ']', $value);
						break;
				}
			}
		}

		// Click the Save Changes button.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to reset the ConvertKit Plugin settings, as if it's a clean installation.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function resetConvertKitPlugin($I)
	{
		// Plugin Settings.
		$I->dontHaveOptionInDatabase('_wp_convertkit_settings');
		$I->dontHaveOptionInDatabase('_wp_convertkit_settings_restrict_content');
		$I->dontHaveOptionInDatabase('convertkit_version');

		// Resources.
		$I->dontHaveOptionInDatabase('convertkit_forms');
		$I->dontHaveOptionInDatabase('convertkit_forms_last_queried');
		$I->dontHaveOptionInDatabase('convertkit_landing_pages');
		$I->dontHaveOptionInDatabase('convertkit_landing_pages_last_queried');
		$I->dontHaveOptionInDatabase('convertkit_posts');
		$I->dontHaveOptionInDatabase('convertkit_posts_last_queried');
		$I->dontHaveOptionInDatabase('convertkit_products');
		$I->dontHaveOptionInDatabase('convertkit_products_last_queried');
		$I->dontHaveOptionInDatabase('convertkit_tags');
		$I->dontHaveOptionInDatabase('convertkit_tags_last_queried');

		// Review Request.
		$I->dontHaveOptionInDatabase('convertkit-review-request');
		$I->dontHaveOptionInDatabase('convertkit-review-dismissed');

		// Upgrades.
		$I->dontHaveOptionInDatabase('_wp_convertkit_upgrade_posts');
	}

	/**
	 * Helper method to load the Plugin's Settings > General screen.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function loadConvertKitSettingsGeneralScreen($I)
	{
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to load the Plugin's Settings > Tools screen.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function loadConvertKitSettingsToolsScreen($I)
	{
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=tools');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to load the Plugin's Settings > Member Content screen.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function loadConvertKitSettingsRestrictContentScreen($I)
	{
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=restrict-content');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Helper method to clear the Plugin's debug log.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function clearDebugLog($I)
	{
		// Go to the Plugin's Tools Screen.
		$I->loadConvertKitSettingsToolsScreen($I);

		// Click the Clear log button.
		$I->click('Clear log');
	}

	/**
	 * Helper method to determine if the given entry exists in the Plugin Debug Log screen's textarea.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I         AcceptanceTester.
	 * @param   string           $entry     Log entry.
	 */
	public function seeInPluginDebugLog($I, $entry)
	{
		$I->loadConvertKitSettingsToolsScreen($I);
		$I->seeInSource($entry);
	}

	/**
	 * Helper method to determine if the given entry does not exist in the Plugin Debug Log screen's textarea.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I         AcceptanceTester.
	 * @param   string           $entry     Log entry.
	 */
	public function dontSeeInPluginDebugLog($I, $entry)
	{
		$I->loadConvertKitSettingsToolsScreen($I);
		$I->dontSeeInSource($entry);
	}

	/**
	 * Helper method to determine that the order of the Form resources in the given
	 * select element are in the expected alphabetical order.
	 *
	 * @since   2.0.8
	 *
	 * @param   AcceptanceTester $I                 AcceptanceTester.
	 * @param   string           $selectElement     <select> element.
	 * @param   bool|array       $prependOptions    Option elements that should appear before the resources.
	 */
	public function checkSelectFormOptionOrder($I, $selectElement, $prependOptions = false)
	{
		// Define options.
		$options = [
			'AAA Test', // First item.
			'WooCommerce Product Form', // Last item.
		];

		// Prepend options, such as 'Default' and 'None' to the options, if required.
		if ( $prependOptions ) {
			$options = array_merge( $prependOptions, $options );
		}

		// Check order.
		$I->checkSelectOptionOrder($I, $selectElement, $options);
	}

	/**
	 * Helper method to determine that the order of the Form resources in the given
	 * select element are in the expected alphabetical order.
	 *
	 * @since   2.0.8
	 *
	 * @param   AcceptanceTester $I                 AcceptanceTester.
	 * @param   string           $selectElement     <select> element.
	 * @param   bool|array       $prependOptions    Option elements that should appear before the resources.
	 */
	public function checkSelectLandingPageOptionOrder($I, $selectElement, $prependOptions = false)
	{
		// Define options.
		$options = [
			'Character Encoding', // First item.
			'Legacy Landing Page', // Last item.
		];

		// Prepend options, such as 'Default' and 'None' to the options, if required.
		if ( $prependOptions ) {
			$options = array_merge( $prependOptions, $options );
		}

		// Check order.
		$I->checkSelectOptionOrder($I, $selectElement, $options);
	}

	/**
	 * Helper method to determine that the order of the Form resources in the given
	 * select element are in the expected alphabetical order.
	 *
	 * @since   2.0.8
	 *
	 * @param   AcceptanceTester $I                 AcceptanceTester.
	 * @param   string           $selectElement     <select> element.
	 * @param   bool|array       $prependOptions    Option elements that should appear before the resources.
	 */
	public function checkSelectTagOptionOrder($I, $selectElement, $prependOptions = false)
	{
		// Define options.
		$options = [
			'gravityforms-tag-1', // First item.
			'wpforms', // Last item.
		];

		// Prepend options, such as 'Default' and 'None' to the options, if required.
		if ( $prependOptions ) {
			$options = array_merge( $prependOptions, $options );
		}

		// Check order.
		$I->checkSelectOptionOrder($I, $selectElement, $options);
	}

	/**
	 * Helper method to determine the order of <option> values for the given select element
	 * and values.
	 *
	 * @since   2.0.8
	 *
	 * @param   AcceptanceTester $I             AcceptanceTester.
	 * @param   string           $selectElement <select> element.
	 * @param   array            $values        <option> values.
	 */
	public function checkSelectOptionOrder($I, $selectElement, $values)
	{
		foreach ( $values as $i => $value ) {
			// Define the applicable CSS selector.
			if ( $i === 0 ) {
				$nth = 'first-child';
			} elseif ( $i + 1 === count( $values ) ) {
				$nth = 'last-child';
			} else {
				$nth = 'nth-child(' . ( $i + 1 ) . ')';
			}

			$I->assertEquals(
				$I->grabTextFrom('select' . $selectElement . ' option:' . $nth),
				$value
			);
		}
	}

	/**
	 * Check that expected HTML exists in the DOM of the page we're viewing for
	 * a Broadcasts block or shortcode, based on its configuration.
	 *
	 * @since   1.9.7.5
	 *
	 * @param   AcceptanceTester $I                      Tester.
	 * @param   bool|int         $numberOfPosts          Number of Broadcasts listed.
	 * @param   bool|string      $seePrevPaginationLabel Test if the "previous" pagination link is output and matches expected label.
	 * @param   bool|string      $seeNextPaginationLabel Test if the "next" pagination link is output and matches expected label.
	 * @param   bool             $seeGrid                Test if the broadcasts are displayed in grid view (false = list view).
	 * @param   bool             $seeImage               Test if the broadcasts display images.
	 * @param   bool             $seeDescription         Test if the broadcasts display descriptions.
	 * @param   bool|string      $seeReadMore            Test if the broadcasts display a read more link matching the given text.
	 */
	public function seeBroadcastsOutput($I, $numberOfPosts = false, $seePrevPaginationLabel = false, $seeNextPaginationLabel = false, $seeGrid = false, $seeImage = false, $seeDescription = false, $seeReadMore = false)
	{
		// Confirm that the block displays.
		$I->seeElementInDOM('div.convertkit-broadcasts');
		$I->seeElementInDOM('div.convertkit-broadcasts ul.convertkit-broadcasts-list');
		$I->seeElementInDOM('div.convertkit-broadcasts ul.convertkit-broadcasts-list li.convertkit-broadcast');
		$I->seeElementInDOM('div.convertkit-broadcasts ul.convertkit-broadcasts-list li.convertkit-broadcast a.convertkit-broadcast-title');

		// Confirm that UTM parameters exist on a broadcast link.
		$I->assertStringContainsString(
			'utm_source=wordpress&utm_term=en_US&utm_content=convertkit',
			$I->grabAttributeFrom('a.convertkit-broadcast-title', 'href')
		);

		// If Display as grid is enabled, confirm the applicable HTML exists so that CSS can style this layout.
		if ($seeGrid) {
			$I->seeElementInDOM('div.convertkit-broadcasts[data-display-grid="1"]');
		} else {
			$I->dontSeeElementInDOM('div.convertkit-broadcasts[data-display-grid="1"]');
		}

		// If Display image is enabled, confirm the image is displayed.
		if ($seeImage) {
			$I->seeElementInDOM('a.convertkit-broadcast-image img');
			$I->assertStringContainsString(
				'utm_source=wordpress&utm_term=en_US&utm_content=convertkit',
				$I->grabAttributeFrom('a.convertkit-broadcast-image', 'href')
			);
		} else {
			$I->dontSeeElementInDOM('a.convertkit-broadcast-image img');
		}

		// If Display description is enabled, confirm the description is displayed.
		if ($seeDescription) {
			$I->seeElementInDOM('.convertkit-broadcast-description');
		} else {
			$I->dontSeeElementInDOM('.convertkit-broadcast-description');
		}

		// If Display read more link is enabled, confirm the read more link is displayed and matches the given text.
		if ($seeReadMore) {
			$I->seeElementInDOM('a.convertkit-broadcast-read-more');
			$I->assertStringContainsString(
				'utm_source=wordpress&utm_term=en_US&utm_content=convertkit',
				$I->grabAttributeFrom('a.convertkit-broadcast-read-more', 'href')
			);
		} else {
			$I->dontSeeElementInDOM('a.convertkit-broadcast-read-more');
		}

		// Confirm that the number of expected broadcasts displays.
		if ($numberOfPosts !== false) {
			$I->seeNumberOfElements('li.convertkit-broadcast', $numberOfPosts);
		}

		// Confirm that previous pagination displays, if expected.
		if ($seePrevPaginationLabel !== false) {
			$I->seeElementInDOM('div.convertkit-broadcasts ul.convertkit-broadcasts-pagination li.convertkit-broadcasts-pagination-prev a');
			$I->seeInSource($seePrevPaginationLabel);
		}

		// Confirm that next pagination displays, if expected.
		if ($seeNextPaginationLabel !== false) {
			$I->seeElementInDOM('div.convertkit-broadcasts ul.convertkit-broadcasts-pagination li.convertkit-broadcasts-pagination-next a');
		}
	}

	/**
	 * Tests that the Broadcasts pagination works, and that the expected Broadcast
	 * is displayed after using previous and next links.
	 *
	 * @since   2.0.0
	 *
	 * @param   AcceptanceTester $I                      Tester.
	 * @param   string           $previousLabel          Previous / Newer Broadcasts Label.
	 * @param   string           $nextLabel              Next / Older Broadcasts Label.
	 */
	public function testBroadcastsPagination($I, $previousLabel, $nextLabel)
	{
		// Confirm that the block displays one broadcast with a pagination link to older broadcasts.
		$I->seeBroadcastsOutput($I, 1, false, $nextLabel);

		// Click the Older Posts link.
		$I->click('li.convertkit-broadcasts-pagination-next a');

		// Wait for the AJAX request to complete, by checking if the convertkit-broadcasts-loading class has been
		// removed from the block.
		$I->waitForBroadcastsToLoad($I);

		// Confirm that the block displays one broadcast with a pagination link to newer broadcasts.
		$I->seeBroadcastsOutput($I, 1, $previousLabel, false);

		// Confirm that the expected Broadcast name is displayed and links to the expected URL, with UTM parameters.
		$I->seeInSource('<a href="' . $_ENV['CONVERTKIT_API_BROADCAST_SECOND_URL'] . '?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit" target="_blank" rel="nofollow noopener"');
		$I->seeInSource($_ENV['CONVERTKIT_API_BROADCAST_SECOND_TITLE']);

		// Click the Newer Posts link.
		$I->click('li.convertkit-broadcasts-pagination-prev a');

		// Wait for the AJAX request to complete, by checking if the convertkit-broadcasts-loading class has been
		// removed from the block.
		$I->waitForBroadcastsToLoad($I);

		// Confirm that the block displays one broadcast with a pagination link to older broadcasts.
		$I->seeBroadcastsOutput($I, 1, false, $nextLabel);

		// Confirm that the expected Broadcast name is displayed and links to the expected URL, with UTM parameters.
		$I->seeInSource('<a href="' . $_ENV['CONVERTKIT_API_BROADCAST_FIRST_URL'] . '?utm_source=wordpress&amp;utm_term=en_US&amp;utm_content=convertkit" target="_blank" rel="nofollow noopener"');
		$I->seeInSource($_ENV['CONVERTKIT_API_BROADCAST_FIRST_TITLE']);
	}

	/**
	 * Wait for the AJAX request to complete, by checking if the convertkit-broadcasts-loading class has been
	 * removed from the block.
	 *
	 * @since   1.9.7.6
	 *
	 * @param   AcceptanceTester $I                      Tester.
	 */
	public function waitForBroadcastsToLoad($I)
	{
		$I->waitForElementChange(
			'div.convertkit-broadcasts',
			function(\Facebook\WebDriver\WebDriverElement $el) {
				return ( strpos($el->getAttribute('class'), 'convertkit-broadcasts-loading') === false ? true : false );
			},
			5
		);
	}

	/**
	 * Check that expected HTML exists in the DOM of the page we're viewing
	 * when a ConvertKit Product link was inserted into a paragraph or button,
	 * and that the button loads the expected ConvertKit Product modal.
	 *
	 * @since   2.0.0
	 *
	 * @param   AcceptanceTester $I              Tester.
	 * @param   string           $productURL     Product URL.
	 * @param   bool|string      $text           Test if the link text matches the given value.
	 */
	public function seeProductLink($I, $productURL, $text = false)
	{
		// Confirm that the commerce.js script exists.
		$I->seeInSource('commerce.js');

		// Confirm that the link exists.
		$I->seeElementInDOM('a[data-commerce]');

		// Confirm that the link points to the correct product.
		$I->assertEquals($productURL, $I->grabAttributeFrom('a[data-commerce]', 'href'));

		// Confirm that the button text is as expected.
		if ($text !== false) {
			$I->seeInSource('>' . $text . '</a>');
		}

		// Click the button to confirm that the ConvertKit modal displays; this confirms
		// necessary ConvertKit scripts have been loaded.
		$I->click('a[href="' . $productURL . '"]');
		$I->seeElementInDOM('iframe[data-active]');
	}

	/**
	 * Returns the expected default settings for Restricted Content.
	 *
	 * @since   2.1.0
	 *
	 * @return  array
	 */
	public function getRestrictedContentDefaultSettings()
	{
		return array(
			'subscribe_text'         => 'This content is only available to premium subscribers',
			'subscribe_button_label' => 'Subscribe',
			'email_text'             => 'Already a premium subscriber? Enter the email address used when purchasing below, to receive a login link to access.',
			'email_button_label'     => 'Send email',
			'email_check_text'       => 'Check your email and click the link to login, or enter the code from the email below.',
			'no_access_text'         => 'Your account does not have access to this content. Please use the button below to purchase, or enter the email address you used to purchase the product.',
		);
	}

	/**
	 * Creates a Page in the database with the given title for restricted content.
	 *
	 * The Page's content comprises of a mix of visible and member's only content.
	 * The default form setting is set to 'None'.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I                          Tester.
	 * @param   string           $title                      Title.
	 * @param   string           $visibleContent             Content that should always be visible.
	 * @param   string           $memberContent              Content that should only be available to authenticated subscribers.
	 * @param   string           $restrictContentSetting     Restrict Content setting.
	 * @return  int                                          Page ID.
	 */
	public function createRestrictedContentPage($I, $title, $visibleContent = 'Visible content.', $memberContent = 'Member only content.', $restrictContentSetting = '')
	{
		return $I->havePostInDatabase(
			[
				'post_type'    => 'page',
				'post_title'   => $title,

				// Emulate Gutenberg content with visible and members only content sections.
				'post_content' => '<!-- wp:paragraph --><p>' . $visibleContent . '</p><!-- /wp:paragraph -->
<!-- wp:more --><!--more--><!-- /wp:more -->
<!-- wp:paragraph -->' . $memberContent . '<!-- /wp:paragraph -->',

				// Don't display a Form on this Page, so we test against Restrict Content's Form.
				'meta_input'   => [
					'_wp_convertkit_post_meta' => [
						'form'             => '-1',
						'landing_page'     => '',
						'tag'              => '',
						'restrict_content' => $restrictContentSetting,
					],
				],
			]
		);
	}

	/**
	 * Run frontend tests for restricted content, to confirm that visible and member's content
	 * is / is not displayed when logging in with valid and invalid subscriber email addresses.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I                  Tester.
	 * @param   string|int       $urlOrPageID        URL or ID of Restricted Content Page.
	 * @param   string           $visibleContent     Content that should always be visible.
	 * @param   string           $memberContent      Content that should only be available to authenticated subscribers.
	 * @param   bool|array       $textItems          Expected text for subscribe text, subscribe button label, email text etc. If not defined, uses expected defaults.
	 */
	public function testRestrictedContentOnFrontend($I, $urlOrPageID, $visibleContent = 'Visible content.', $memberContent = 'Member only content.', $textItems = false)
	{
		// Define expected text and labels if not supplied.
		if ( ! $textItems ) {
			$textItems = $this->getRestrictedContentDefaultSettings();
		}

		// Navigate to the page.
		if ( is_numeric( $urlOrPageID ) ) {
			$I->amOnPage('?p=' . $urlOrPageID);
		} else {
			$I->amOnUrl($urlOrPageID);
		}

		// Confirm Restrict Content CSS is output.
		$I->seeInSource('<link rel="stylesheet" id="convertkit-restrict-content-css" href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/plugins/convertkit/resources/frontend/css/restrict-content.css');

		// Check content is / is not displayed, and CTA displays with expected text.
		$this->testRestrictContentHidesContentWithCTA($I, $visibleContent, $memberContent, $textItems);

		// Login as a ConvertKit subscriber who does not exist in ConvertKit.
		$I->waitForElementVisible('input#convertkit_email');
		$I->fillField('convertkit_email', 'fail@convertkit.com');
		$I->click('input.wp-block-button__link');

		// Check content is / is not displayed, and CTA displays with expected text.
		$I->see('Email address is invalid'); // Response from the API.
		$this->testRestrictContentHidesContentWithCTA($I, $visibleContent, $memberContent, $textItems);

		// Set cookie with signed subscriber ID and reload the restricted content page, as if we entered the
		// code sent in the email as a ConvertKit subscriber who has not subscribed to the product.
		$I->setCookie('ck_subscriber_id', $_ENV['CONVERTKIT_API_SIGNED_SUBSCRIBER_ID_NO_ACCESS']);
		if ( is_numeric( $urlOrPageID ) ) {
			$I->amOnPage('?p=' . $urlOrPageID . '&ck-cache-bust=' . microtime() );
		} else {
			$I->amOnUrl($urlOrPageID . '?ck-cache-bust=' . microtime() );
		}

		// Check content is / is not displayed, and CTA displays with expected text.
		$I->see($textItems['no_access_text']);
		$this->testRestrictContentHidesContentWithCTA($I, $visibleContent, $memberContent, $textItems);

		// Login as a ConvertKit subscriber who has subscribed to the product.
		$I->waitForElementVisible('input#convertkit_email');
		$I->fillField('convertkit_email', $_ENV['CONVERTKIT_API_SUBSCRIBER_EMAIL']);
		$I->click('input.wp-block-button__link');

		// Confirm that confirmation an email has been sent is displayed.
		$this->testRestrictContentShowsEmailCodeForm($I, $visibleContent, $memberContent);

		// Test that the restricted content displays when a valid signed subscriber ID is used,
		// as if we entered the code sent in the email.
		$this->testRestrictedContentShowsContentWithValidSubscriberID($I, $urlOrPageID, $visibleContent, $memberContent);
	}

	/**
	 * Run frontend tests for restricted content, to confirm that both visible and member content is displayed
	 * when a valid signed subscriber ID is set as a cookie, as if the user entered a code sent in the email.
	 *
	 * @since   2.2.2
	 *
	 * @param   AcceptanceTester $I                  Tester.
	 * @param   string|int       $urlOrPageID        URL or ID of Restricted Content Page.
	 * @param   string           $visibleContent     Content that should always be visible.
	 * @param   string           $memberContent      Content that should only be available to authenticated subscribers.
	 */
	public function testRestrictedContentShowsContentWithValidSubscriberID($I, $urlOrPageID, $visibleContent, $memberContent)
	{
		// Set cookie with signed subscriber ID, as if we entered the code sent in the email.
		$I->setCookie('ck_subscriber_id', $_ENV['CONVERTKIT_API_SIGNED_SUBSCRIBER_ID']);

		// Reload the restricted content page.
		if ( is_numeric( $urlOrPageID ) ) {
			$I->amOnPage('?p=' . $urlOrPageID );
		} else {
			$I->amOnUrl($urlOrPageID );
		}

		// Confirm cookie was set with the expected value.
		$I->assertEquals($I->grabCookie('ck_subscriber_id'), $_ENV['CONVERTKIT_API_SIGNED_SUBSCRIBER_ID']);

		// Confirm that the restricted content is now displayed, as we've authenticated as a subscriber
		// who has access to this Product.
		$I->testRestrictContentDisplaysContent($I, $visibleContent, $memberContent);
	}

	/**
	 * Run frontend tests for restricted content, to confirm that:
	 * - visible content is displayed,
	 * - member's content is not displayed,
	 * - the CTA is displayed with the expected text
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I                  Tester.
	 * @param   string           $visibleContent     Content that should always be visible.
	 * @param   string           $memberContent      Content that should only be available to authenticated subscribers.
	 * @param   bool|array       $textItems          Expected text for subscribe text, subscribe button label, email text etc. If not defined, uses expected defaults.
	 */
	public function testRestrictContentHidesContentWithCTA($I, $visibleContent = 'Visible content.', $memberContent = 'Member only content.', $textItems = false)
	{
		// Define expected text and labels if not supplied.
		if ( ! $textItems ) {
			$textItems = $this->getRestrictedContentDefaultSettings();
		}

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the visible text displays, hidden text does not display and the CTA displays.
		$I->see($visibleContent);
		$I->dontSee($memberContent);

		// Confirm that the CTA displays with the expected text.
		$I->seeElementInDOM('#convertkit-restrict-content');
		$I->see($textItems['subscribe_text']);
		$I->see($textItems['subscribe_button_label']);
		$I->see($textItems['email_text']);
		$I->seeInSource('<input type="submit" class="wp-block-button__link wp-block-button__link" value="' . $textItems['email_button_label'] . '">');
	}

	/**
	 * Run frontend tests for restricted content, to confirm that:
	 * - visible content is displayed,
	 * - member's content is not displayed,
	 * - the email code form is displayed with the expected text.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I                  Tester.
	 * @param   string           $visibleContent     Content that should always be visible.
	 * @param   string           $memberContent      Content that should only be available to authenticated subscribers.
	 */
	public function testRestrictContentShowsEmailCodeForm($I, $visibleContent = 'Visible content.', $memberContent = 'Member only content.')
	{
		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the visible text displays, hidden text does not display and the CTA displays.
		$I->see($visibleContent);
		$I->dontSee($memberContent);

		// Confirm that the CTA displays with the expected text.
		$I->seeElementInDOM('#convertkit-restrict-content');
		$I->seeElementInDOM('input#convertkit_subscriber_code');
		$I->seeElementInDOM('input.wp-block-button__link');
	}

	/**
	 * Run frontend tests for restricted content, to confirm that:
	 * - visible content is displayed,
	 * - member's content is displayed,
	 * - the CTA is not displayed
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I                  Tester.
	 * @param   string           $visibleContent     Content that should always be visible.
	 * @param   string           $memberContent      Content that should only be available to authenticated subscribers.
	 */
	public function testRestrictContentDisplaysContent($I, $visibleContent = 'Visible content.', $memberContent = 'Member only content.')
	{
		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the visible and hidden text displays.
		$I->see($visibleContent);
		$I->see($memberContent);

		// Confirm that the CTA is not displayed.
		$I->dontSeeElementInDOM('#convertkit-restrict-content');
	}

	/**
	 * Check that expected HTML exists in the DOM of the page we're viewing for
	 * a Form Trigger block or shortcode, and that the button loads the expected
	 * ConvertKit Form.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I              Tester.
	 * @param   string           $formURL        Form URL.
	 * @param   bool|string      $text           Test if the button text matches the given value.
	 * @param   bool|string      $textColor      Test if the given text color is applied.
	 * @param   bool|string      $backgroundColor Test is the given background color is applied.
	 */
	public function seeFormTriggerOutput($I, $formURL, $text = false, $textColor = false, $backgroundColor = false)
	{
		// Confirm that the button stylesheet loaded.
		$I->seeInSource('<link rel="stylesheet" id="convertkit-button-css" href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/plugins/convertkit/resources/frontend/css/button.css');

		// Confirm that the block displays.
		$I->seeElementInDOM('a.convertkit-formtrigger.wp-block-button__link');

		// Confirm that the button links to the correct form.
		$I->assertEquals($formURL, $I->grabAttributeFrom('a.convertkit-formtrigger', 'href'));

		// Confirm that the text is as expected.
		if ($text !== false) {
			$I->see($text);
		}

		// Confirm that the text color is as expected.
		if ($textColor !== false) {
			$I->seeElementInDOM('a.convertkit-formtrigger.has-text-color');
			$I->assertStringContainsString(
				'color:' . $textColor,
				$I->grabAttributeFrom('a.convertkit-formtrigger', 'style')
			);
		}

		// Confirm that the background color is as expected.
		if ($backgroundColor !== false) {
			$I->seeElementInDOM('a.convertkit-formtrigger.has-background');
			$I->assertStringContainsString(
				'background-color:' . $backgroundColor,
				$I->grabAttributeFrom('a.convertkit-formtrigger', 'style')
			);
		}

		// Click the button to confirm that the ConvertKit modal displays.
		$I->click('a.convertkit-formtrigger');
		$I->waitForElementVisible('div.formkit-overlay');
	}

	/**
	 * Check that expected HTML does not exist in the DOM of the page we're viewing for
	 * a Form Trigger block or shortcode.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I      Tester.
	 */
	public function dontSeeFormTriggerOutput($I)
	{
		// Confirm that the block does not display.
		$I->dontSeeElementInDOM('div.wp-block-button a.convertkit-formtrigger');
	}

	/**
	 * Check that expected HTML exists in the DOM of the page we're viewing for
	 * a Form Trigger link, and that the link loads the expected
	 * ConvertKit Form.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I              Tester.
	 * @param   string           $formURL        Form URL.
	 * @param   bool|string      $text           Test if the text matches the given value.
	 */
	public function seeFormTriggerLinkOutput($I, $formURL, $text = false)
	{
		// Confirm that the link displays.
		$I->seeElementInDOM('a.convertkit-form-link');

		// Confirm that the button links to the correct form.
		$I->assertEquals($formURL, $I->grabAttributeFrom('a.convertkit-form-link', 'href'));

		// Confirm that the text is as expected.
		if ($text !== false) {
			$I->see($text);
		}

		// Click the link to confirm that the ConvertKit form displays.
		$I->click('a.convertkit-form-link');
		$I->waitForElementVisible('div.formkit-overlay');
	}

	/**
	 * Check that expected HTML does not exist in the DOM of the page we're viewing for
	 * a Form Trigger link formatter.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I      Tester.
	 */
	public function dontSeeFormTriggerLinkOutput($I)
	{
		// Confirm that the link does not display.
		$I->dontSeeElementInDOM('a.convertkit-form-link');
	}

	/**
	 * Check that expected HTML exists in the DOM of the page we're viewing for
	 * a Product block or shortcode, and that the button loads the expected
	 * ConvertKit Product modal.
	 *
	 * @since   1.9.8.5
	 *
	 * @param   AcceptanceTester $I              Tester.
	 * @param   string           $productURL     Product URL.
	 * @param   bool|string      $text           Test if the button text matches the given value.
	 * @param   bool|string      $textColor      Test if the given text color is applied.
	 * @param   bool|string      $backgroundColor Test is the given background color is applied.
	 */
	public function seeProductOutput($I, $productURL, $text = false, $textColor = false, $backgroundColor = false)
	{
		// Confirm that the product stylesheet loaded.
		$I->seeInSource('<link rel="stylesheet" id="convertkit-button-css" href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-content/plugins/convertkit/resources/frontend/css/button.css');

		// Confirm that the block displays.
		$I->seeElementInDOM('a.convertkit-product.wp-block-button__link');

		// Confirm that the button links to the correct product.
		$I->assertEquals($productURL, $I->grabAttributeFrom('a.convertkit-product', 'href'));

		// Confirm that the text is as expected.
		if ($text !== false) {
			$I->see($text);
		}

		// Confirm that the text color is as expected.
		if ($textColor !== false) {
			$I->seeElementInDOM('a.convertkit-product.has-text-color');
			$I->assertStringContainsString(
				'color:' . $textColor,
				$I->grabAttributeFrom('a.convertkit-product', 'style')
			);
		}

		// Confirm that the background color is as expected.
		if ($backgroundColor !== false) {
			$I->seeElementInDOM('a.convertkit-product.has-background');
			$I->assertStringContainsString(
				'background-color:' . $backgroundColor,
				$I->grabAttributeFrom('a.convertkit-product', 'style')
			);
		}

		// Click the button to confirm that the ConvertKit modal displays; this confirms
		// necessary ConvertKit scripts have been loaded.
		$I->click('a.convertkit-product');
		$I->seeElementInDOM('iframe[data-active]');
	}

	/**
	 * Check that expected HTML does not exist in the DOM of the page we're viewing for
	 * a Product block or shortcode.
	 *
	 * @since   1.9.8.5
	 *
	 * @param   AcceptanceTester $I      Tester.
	 */
	public function dontSeeProductOutput($I)
	{
		// Confirm that the block does not display.
		$I->dontSeeElementInDOM('div.wp-block-button a.convertkit-product');
	}

	/**
	 * Test that the 'Click here to add your API Key' link displays a popup window,
	 * when using a block with no API Keys specified.
	 *
	 * @since   2.2.6
	 *
	 * @param   AcceptanceTester $I                 Tester.
	 * @param   string           $blockName         Block Name.
	 * @param   bool|string      $expectedMessage   Expected message displayed in block after entering valid API Keys.
	 */
	public function testBlockNoAPIKeyPopupWindow($I, $blockName, $expectedMessage = false)
	{
		// Confirm that the Form block displays instructions to the user on how to enter their API Key.
		$I->see(
			'No API Key specified.',
			[
				'css' => '.convertkit-no-content',
			]
		);

		// Click the link to confirm it loads the Plugin's setup wizard.
		$I->click(
			'Click here to add your API Key.',
			[
				'css' => '.convertkit-no-content',
			]
		);

		// Switch to the window that just opened.
		$I->switchToWindow('convertkit_popup_window');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm no logo or progress bar is displayed, as this is the modal version of the wizard.
		$I->dontSeeElementInDOM('#convertkit-setup-wizard-header');

		// Confirm no exit wizard link is displayed.
		$I->dontSeeElementInDOM('#convertkit-setup-wizard-exit-link');

		// Confirm expected title is displayed.
		$I->see('Welcome to the ConvertKit Setup Wizard');

		// Confirm Step text is correct.
		$I->see('Step 1 of 2');

		// Test Connect button.
		$I->click('Connect');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm no logo or progress bar is displayed, as this is the modal version of the wizard.
		$I->dontSeeElementInDOM('#convertkit-setup-wizard-header');

		// Confirm no exit wizard link is displayed.
		$I->dontSeeElementInDOM('#convertkit-setup-wizard-exit-link');

		// Confirm expected title is displayed.
		$I->see('Connect your ConvertKit account');

		// Confirm Step text is correct.
		$I->see('Step 2 of 2');

		// Confirm Back and Connect buttons display.
		$I->seeElementInDOM('#convertkit-setup-wizard-footer div.left a.button');
		$I->seeElementInDOM('#convertkit-setup-wizard-footer div.right button');

		// Fill fields with valid API Keys.
		$I->fillField('api_key', $_ENV['CONVERTKIT_API_KEY']);
		$I->fillField('api_secret', $_ENV['CONVERTKIT_API_SECRET']);

		// Click Connect button.
		$I->click('Connect');

		// Switch back to the main browser window.
		$I->switchToWindow();

		// Wait until the block changes to refreshing.
		$I->waitForElementVisible('.' . $blockName . ' span.spinner', 5);

		// Wait for the refresh button to disappear, confirming that the block refresh completed
		// and that resources now exist.
		$I->waitForElementNotVisible('button.convertkit-block-refresh');

		// Confirm that the block displays the expected message.
		if ($expectedMessage) {
			$I->see(
				$expectedMessage,
				[
					'css' => '.convertkit-no-content',
				]
			);
		}
	}

	/**
	 * Check that the given Page does output the Creator Network Recommendations
	 * script.
	 *
	 * @since   2.2.7
	 *
	 * @param   AcceptanceTester $I             AcceptanceTester.
	 * @param   int              $pageID        Page ID.
	 */
	public function seeCreatorNetworkRecommendationsScript($I, $pageID)
	{
		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm the recommendations script was not loaded.
		$I->seeInSource('recommendations.js');
	}

	/**
	 * Check that the given Page does not output the Creator Network Recommendations
	 * script.
	 *
	 * @since   2.2.7
	 *
	 * @param   AcceptanceTester $I             AcceptanceTester.
	 * @param   int              $pageID        Page ID.
	 */
	public function dontSeeCreatorNetworkRecommendationsScript($I, $pageID)
	{
		// Load the Page on the frontend site.
		$I->amOnPage('/?p=' . $pageID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm the recommendations script was not loaded.
		$I->dontSeeInSource('recommendations.js');
	}

	/**
	 * Selects all text for the given input field.
	 *
	 * @since   2.2.0
	 *
	 * @param   AcceptanceTester $I         Acceptance Tester.
	 * @param   string           $selector  CSS or ID selector for the input element.
	 */
	public function selectAllText($I, $selector)
	{
		// Determine whether to use the control or command key, depending on the OS.
		$key = \Facebook\WebDriver\WebDriverKeys::CONTROL;

		// If we're on OSX, use the command key instead.
		if (array_key_exists('TERM_PROGRAM', $_SERVER) && strpos( $_SERVER['TERM_PROGRAM'], 'Apple') !== false) {
			$key = \Facebook\WebDriver\WebDriverKeys::COMMAND;
		}

		// Press Ctrl/Command + a on Keyboard.
		$I->pressKey($selector, array( $key, 'a' ));
	}
}
