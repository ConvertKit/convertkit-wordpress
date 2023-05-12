<?php
/**
 * Tests that common caching plugins do not interfere with Restrict Content
 * output when configured correctly.
 *
 * @since   2.2.1
 */
class RestrictContentCacheCest
{
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
		$I->activeAndEnableWPFastestCachePlugin($I);
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
		// Activate and enable WP-Optimize Plugin.
		$I->activeAndEnableWPOptimizePlugin($I);
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
		$I->activeAndEnableWPSuperCachePlugin($I);
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
