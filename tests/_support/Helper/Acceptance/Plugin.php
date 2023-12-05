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
	 * @param   AcceptanceTester $I                     AcceptanceTester.
	 * @param   bool|string      $apiKey                API Key (if specified, used instead of CONVERTKIT_API_KEY).
	 * @param   bool|string      $apiSecret             API Secret (if specified, used instead of CONVERTKIT_API_SECRET).
	 * @param   bool|string      $pageFormID            Default Form ID for Pages (if specified, used instead of CONVERTKIT_API_FORM_ID).
	 * @param   bool|string      $postFormID            Default Form ID for Posts (if specified, used instead of CONVERTKIT_API_FORM_ID).
	 * @param   bool|string      $productFormID         Default Form ID for Products (if specified, used instead of CONVERTKIT_API_FORM_ID).
	 * @param   bool             $disableJS             Disable JS.
	 * @param   bool             $disableCSS            Disable CSS.
	 * @param   bool|string      $globalNonInlineFormID Default Global non-inline Form ID (if specified, none if false).
	 */
	public function setupConvertKitPlugin($I, $options = false)
	{
		// Define default options.
		$defaults = [
			'api_key'         => $_ENV['CONVERTKIT_API_KEY'],
			'api_secret'      => $_ENV['CONVERTKIT_API_SECRET'],
			'debug'           => 'on',
			'no_scripts'      => '',
			'no_css'          => '',
			'post_form'       => $_ENV['CONVERTKIT_API_FORM_ID'],
			'page_form'       => $_ENV['CONVERTKIT_API_FORM_ID'],
			'product_form'    => $_ENV['CONVERTKIT_API_FORM_ID'],
			'non_inline_form' => '',
		];

		// If supplied options are an array, merge them with the defaults.
		if (is_array($options)) {
			$options = array_merge($defaults, $options);
		}

		// Define settings in options table.
		$I->haveOptionInDatabase('_wp_convertkit_settings', $options);
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
	 * Helper method to programmatically setup the Plugin's Member Content settings.
	 *
	 * @since   2.1.0
	 *
	 * @param   AcceptanceTester $I          AcceptanceTester.
	 * @param   bool|array       $settings   Array of key/value settings.
	 */
	public function setupConvertKitPluginRestrictContent($I, $settings)
	{
		$I->haveOptionInDatabase(
			'_wp_convertkit_settings_restrict_content',
			array_merge(
				$settings,
				$I->getRestrictedContentDefaultSettings()
			)
		);
	}

	/**
	 * Helper method to setup the Plugin's Broadcasts to Posts settings.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I          AcceptanceTester.
	 * @param   bool|array       $settings   Array of key/value settings. If not defined, uses expected defaults.
	 */
	public function setupConvertKitPluginBroadcasts($I, $settings = false)
	{
		// Go to the Plugin's Broadcasts screen.
		$I->loadConvertKitSettingsBroadcastsScreen($I);

		// Complete fields.
		if ( $settings ) {
			foreach ( $settings as $key => $value ) {
				switch ( $key ) {
					case 'enabled':
					case 'enabled_export':
					case 'no_styles':
						if ( $value ) {
							$I->checkOption('_wp_convertkit_settings_broadcasts[' . $key . ']');
						} else {
							$I->uncheckOption('_wp_convertkit_settings_broadcasts[' . $key . ']');
						}
						break;

					case 'post_status':
					case 'category_id':
					case 'author_id':
						$I->fillSelect2Field($I, '#select2-_wp_convertkit_settings_broadcasts_' . $key . '-container', $value);

						break;
					default:
						$I->fillField('_wp_convertkit_settings_broadcasts[' . $key . ']', $value);
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
		$I->dontHaveOptionInDatabase('_wp_convertkit_settings_broadcasts');
		$I->dontHaveOptionInDatabase('convertkit_version');

		// Resources.
		$I->dontHaveOptionInDatabase('convertkit_broadcasts');
		$I->dontHaveOptionInDatabase('convertkit_broadcasts_last_queried');
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
	 * Helper method to load the Plugin's Settings > Broadcasts screen.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 */
	public function loadConvertKitSettingsBroadcastsScreen($I)
	{
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=broadcasts');

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
			'AAA Test [inline]', // First item.
			'WooCommerce Product Form [inline]', // Last item.
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
