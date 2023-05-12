<?php
/**
 * Tests that common caching plugins do not interfere with Restrict Content
 * output when configured correctly.
 *
 * @since   2.2.1
 */
class RestrictContentCacheCest
{
	public $visibleContent = 'Visible content';

	public $memberContent = 'Member only content.';

	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate and Setup ConvertKit plugin.
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);

		// Enable Restricted Content.
		$I->setupConvertKitPluginRestrictContent(
			$I,
			[
				'enabled' => true,
			]
		);

		// Clear up any cache configuration files that might exist from previous tests.
		$I->deleteWPCacheConfigFiles($I);
	}

	/**
	 * Tests that the LiteSpeed Cache Plugin does not interfere with Restrict Content
	 * output when a ck_subscriber_id cookie is present.
	 * 
	 * @since 	2.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentLiteSpeedCache(AcceptanceTester $I)
	{
		// Activate and enable LiteSpeed Cache Plugin.
		$I->activeAndEnableLiteSpeedCachePlugin($I);

		// Create Restricted Content Page.
		$pageID = $I->createRestrictedContentPage(
			$I,
			'ConvertKit: Restrict Content: Product: LiteSpeed Cache',
			$this->visibleContent,
			$this->memberContent,
			'product_' . $_ENV['CONVERTKIT_API_PRODUCT_ID']
		);

		// Log out, so that caching is honored.
		$I->logOut();

		// Navigate to the page.
		$I->amOnPage('?p=' . $pageID);

		// Test that the restricted content CTA displays when no valid signed subscriber ID is used,
		// to confirm caching does not show member only content.
		$I->testRestrictContentHidesContentWithCTA($I, $this->visibleContent, $this->memberContent);

		// Test that the restricted content displays when a valid signed subscriber ID is used,
		// to confirm caching does not show the incorrect content.
		$I->testRestrictedContentShowsContentWithValidSubscriberID($I, $pageID, $this->visibleContent, $this->memberContent);
	}

	/**
	 * Tests that the W3 Total Cache Plugin does not interfere with Restrict Content
	 * output when a ck_subscriber_id cookie is present.
	 * 
	 * @since 	2.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentW3TotalCache(AcceptanceTester $I)
	{
		// Activate and enable W3 Total Cache Plugin.
		$I->activeAndEnableW3TotalCachePlugin($I);
	}

	/**
	 * Tests that the WP Fatest Cache Plugin does not interfere with Restrict Content
	 * output when a ck_subscriber_id cookie is present.
	 * 
	 * @since 	2.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentWPFastestCache(AcceptanceTester $I)
	{
		// Activate and enable WP Fastest Cache Plugin.
		$I->activateThirdPartyPlugin($I, 'wp-fastest-cache');
		$I->enableCachingWPFastestCachePlugin($I);

		// No need to configure WP-Optimize to exclude caching when the ck_subscriber_id cookie is set,
		// as this is automatically performed by the ConvertKit Plugin.

		// Create Restricted Content Page.
		$pageID = $I->createRestrictedContentPage(
			$I,
			'ConvertKit: Restrict Content: Product: WP Fastest Cache',
			$this->visibleContent,
			$this->memberContent,
			'product_' . $_ENV['CONVERTKIT_API_PRODUCT_ID']
		);

		// Log out, so that caching is honored.
		$I->logOut();

		// Navigate to the page.
		$I->amOnPage('?p=' . $pageID);

		// Test that the restricted content CTA displays when no valid signed subscriber ID is used,
		// to confirm caching does not show member only content.
		$I->testRestrictContentHidesContentWithCTA($I, $this->visibleContent, $this->memberContent);

		// Test that the restricted content displays when a valid signed subscriber ID is used,
		// to confirm caching does not show the incorrect content.
		$I->testRestrictedContentShowsContentWithValidSubscriberID($I, $pageID, $this->visibleContent, $this->memberContent);
	}

	/**
	 * Tests that the WP-Optimize Plugin does not interfere with Restrict Content
	 * output when a ck_subscriber_id cookie is present.
	 * 
	 * @since 	2.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentWPOptimize(AcceptanceTester $I)
	{
		// Activate and enable WP Super Cache Plugin.
		$I->activateThirdPartyPlugin($I, 'wp-optimize');
		$I->enableCachingWPOptimizePlugin($I);

		// No need to configure WP-Optimize to exclude caching when the ck_subscriber_id cookie is set,
		// as this is automatically performed by the ConvertKit Plugin.

		// Create Restricted Content Page.
		$pageID = $I->createRestrictedContentPage(
			$I,
			'ConvertKit: Restrict Content: Product: WP-Optimize',
			$this->visibleContent,
			$this->memberContent,
			'product_' . $_ENV['CONVERTKIT_API_PRODUCT_ID']
		);

		// Log out, so that caching is honored.
		$I->logOut();

		// Navigate to the page.
		$I->amOnPage('?p=' . $pageID);

		// Test that the restricted content CTA displays when no valid signed subscriber ID is used,
		// to confirm caching does not show member only content.
		$I->testRestrictContentHidesContentWithCTA($I, $this->visibleContent, $this->memberContent);

		// Test that the restricted content displays when a valid signed subscriber ID is used,
		// to confirm caching does not show the incorrect content.
		$I->testRestrictedContentShowsContentWithValidSubscriberID($I, $pageID, $this->visibleContent, $this->memberContent);
	}

	/**
	 * Tests that the WP Super Cache Plugin does not interfere with Restrict Content
	 * output when a ck_subscriber_id cookie is present.
	 * 
	 * @since 	2.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentWPSuperCache(AcceptanceTester $I)
	{
		// Activate and enable WP Super Cache Plugin.
		$I->activateThirdPartyPlugin($I, 'wp-super-cache');
		$I->enableCachingWPSuperCachePlugin($I);

		// Configure WP Super Cache Plugin to exclude caching when the ck_subscriber_id cookie is set.
		$I->excludeCachingWPSuperCachePlugin($I);

		// Create Restricted Content Page.
		$pageID = $I->createRestrictedContentPage(
			$I,
			'ConvertKit: Restrict Content: Product: WP Super Cache',
			$this->visibleContent,
			$this->memberContent,
			'product_' . $_ENV['CONVERTKIT_API_PRODUCT_ID']
		);

		// Log out, so that caching is honored.
		$I->logOut();

		// Navigate to the page.
		$I->amOnPage('?p=' . $pageID);

		// Test that the restricted content CTA displays when no valid signed subscriber ID is used,
		// to confirm caching does not show member only content.
		$I->testRestrictContentHidesContentWithCTA($I, $this->visibleContent, $this->memberContent);

		// Test that the restricted content displays when a valid signed subscriber ID is used,
		// to confirm caching does not show the incorrect content.
		$I->testRestrictedContentShowsContentWithValidSubscriberID($I, $pageID, $this->visibleContent, $this->memberContent);
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->resetCookie('ck_subscriber_id');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
