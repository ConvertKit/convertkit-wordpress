<?php
/**
 * Tests that the ck_subscriber_id is removed from the URL by the Plugin's JS.
 *
 * @since   2.5.7
 */
class SubscriberIDURLCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);
	}

	/**
	 * Test that the ck_subscriber_id parameter is removed from the URL.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testSubscriberIDRemovedFromURL(AcceptanceTester $I)
	{
		// Create Page.
		$I->havePageInDatabase(
			[
				'post_name'    => 'convertkit-subscriber-id-url',
				'post_content' => 'Test',
			]
		);

		// Confirm that the ck_subscriber_id was removed.
		$I->amOnPage('/convertkit-subscriber-id-url?ck_subscriber_id=' . $_ENV['CONVERTKIT_API_SUBSCRIBER_ID']);
		$I->checkNoWarningsAndNoticesOnScreen($I);
		$I->wait(2);
		$I->assertStringNotContainsString('ck_subscriber_id=' . $_ENV['CONVERTKIT_API_SUBSCRIBER_ID'], $I->grabFromCurrentUrl());

		// Load the Page with UTM parameters at the end.
		$I->amOnPage('/convertkit-subscriber-id-url?ck_subscriber_id=' . $_ENV['CONVERTKIT_API_SUBSCRIBER_ID'] . '&utm_source=email&utm_medium=email');
		$I->checkNoWarningsAndNoticesOnScreen($I);
		$I->wait(2);
		$I->assertStringNotContainsString('ck_subscriber_id=' . $_ENV['CONVERTKIT_API_SUBSCRIBER_ID'], $I->grabFromCurrentUrl());
		$I->assertStringContainsString('?utm_source=email&utm_medium=email', $I->grabFromCurrentUrl());

		// Load the Page with UTM parameters at the start.
		$I->amOnPage('/convertkit-subscriber-id-url?utm_source=email&utm_medium=email&ck_subscriber_id=' . $_ENV['CONVERTKIT_API_SUBSCRIBER_ID']);
		$I->checkNoWarningsAndNoticesOnScreen($I);
		$I->wait(2);
		$I->assertStringNotContainsString('ck_subscriber_id=' . $_ENV['CONVERTKIT_API_SUBSCRIBER_ID'], $I->grabFromCurrentUrl());
		$I->assertStringContainsString('?utm_source=email&utm_medium=email', $I->grabFromCurrentUrl());
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.5.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
