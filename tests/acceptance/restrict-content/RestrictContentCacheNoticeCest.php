<?php
/**
 * Tests that common caching plugins display a notice in the
 * WordPress Administration until the caching plugin is configured.
 *
 * @since   2.2.1
 */
class RestrictContentCacheNoticeCest
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
	 * Tests that no WordPress Admin notice is displayed for each caching plugin when:
	 * - Restrict Content is disabled, and
	 * - each caching Plugin is activated.
	 * 
	 * @since 	2.2.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function tesRestrictContentNoticeNotDisplayedWhenDisabled(AcceptanceTester $I)
	{
		
	}

	/**
	 * Tests that no WordPress Admin notice is displayed when:
	 * - Restrict Content is enabled,
	 * - LiteSpeed Cache Plugin is active, and
	 * - LiteSpeed Cache caching is disabled.
	 * 
	 * @since 	2.2.1
	 * 
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentNoticeNotDisplayedWhenLiteSpeedCacheDisabled(AcceptanceTester $I)
	{

	}

	/**
	 * Tests that no WordPress Admin notice is displayed when:
	 * - Restrict Content is enabled,
	 * - LiteSpeed Cache Plugin is active,
	 * - LiteSpeed Cache caching is enabled, and
	 * - LiteSpeed Cache's "Do Not Cache Cookies" setting does contain ck_subscriber_id.
	 * 
	 * @since 	2.2.1
	 * 
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentNoticeDisplayedWhenLiteSpeedCacheEnabledAndConfigured(AcceptanceTester $I)
	{

	}

	/**
	 * Tests that a WordPress Admin notice is displayed when:
	 * - Restrict Content is enabled,
	 * - LiteSpeed Cache Plugin is active,
	 * - LiteSpeed Cache caching is enabled, and
	 * - LiteSpeed Cache's "Do Not Cache Cookies" setting does not contain ck_subscriber_id.
	 * 
	 * @since 	2.2.1
	 * 
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentNoticeDisplayedWhenLiteSpeedCacheEnabled(AcceptanceTester $I)
	{

	}

	/**
	 * Tests that no WordPress Admin notice is displayed when:
	 * - Restrict Content is enabled,
	 * - W3 Total Cache Plugin is active, and
	 * - W3 Total Cache caching is disabled.
	 * 
	 * @since 	2.2.1
	 * 
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentNoticeNotDisplayedWhenW3TotalCacheDisabled(AcceptanceTester $I)
	{

	}

	/**
	 * Tests that no WordPress Admin notice is displayed when:
	 * - Restrict Content is enabled,
	 * - W3 Total Cache Plugin is active,
	 * - W3 Total Cache caching is enabled, and
	 * - W3 Total Cache's "Rejected Cookies" setting does contain ck_subscriber_id.
	 * 
	 * @since 	2.2.1
	 * 
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentNoticeDisplayedWhenW3TotalCacheEnabledAndConfigured(AcceptanceTester $I)
	{

	}

	/**
	 * Tests that a WordPress Admin notice is displayed when:
	 * - Restrict Content is enabled,
	 * - W3 Total Cache Plugin is active,
	 * - W3 Total Cache caching is enabled, and
	 * - W3 Total Cache's "Rejected Cookies" setting does not contain ck_subscriber_id.
	 * 
	 * @since 	2.2.1
	 * 
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentNoticeDisplayedWhenW3TotalCacheEnabled(AcceptanceTester $I)
	{

	}

	/**
	 * Tests that no WordPress Admin notice is displayed when:
	 * - Restrict Content is enabled,
	 * - WP Super Cache Plugin is active, and
	 * - WP Super Cache caching is disabled.
	 * 
	 * @since 	2.2.1
	 * 
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentNoticeNotDisplayedWhenWPSuperCacheDisabled(AcceptanceTester $I)
	{

	}

	/**
	 * Tests that no WordPress Admin notice is displayed when:
	 * - Restrict Content is enabled,
	 * - WP Super Cache Plugin is active,
	 * - WP Super Cache caching is enabled, and
	 * - WP Super Cache's "Rejected Cookies" setting does contain ck_subscriber_id.
	 * 
	 * @since 	2.2.1
	 * 
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentNoticeDisplayedWhenWPSuperCacheEnabledAndConfigured(AcceptanceTester $I)
	{

	}

	/**
	 * Tests that a WordPress Admin notice is displayed when:
	 * - Restrict Content is enabled,
	 * - WP Super Cache Plugin is active,
	 * - WP Super Cache caching is enabled, and
	 * - WP Super Cache's "Rejected Cookies" setting does not contain ck_subscriber_id.
	 * 
	 * @since 	2.2.1
	 * 
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRestrictContentNoticeDisplayedWhenWPSuperCacheEnabled(AcceptanceTester $I)
	{

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
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
