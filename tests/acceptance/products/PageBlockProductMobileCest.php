<?php
/**
 * Tests for the ConvertKit Product Gutenberg Block.
 *
 * @since   2.4.1
 */
class PageBlockProductMobileCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.4.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->changeUserAgent($_ENV['TEST_SITE_HTTP_USER_AGENT_MOBILE']);
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPluginNoDefaultForms($I);
		$I->setupConvertKitPluginResources($I);
	}

	/**
	 * Test the Product block opens the ConvertKit Product in the same window instead
	 * of a modal when the Disable modal on mobile option is enabled.
	 *
	 * @since   2.4.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testProductBlockWithDisableModalOnMobileParameterEnabled(AcceptanceTester $I)
	{
		// Create Page.
		$pageID = $I->havePageInDatabase(
			[
				'post_title'   => 'ConvertKit: Page: Product: Disable Modal on Mobile',
				'post_name'    => 'convertkit-page-product-disable-modal-on-mobile',
				'post_content' => '<!-- wp:convertkit/product {"product":"' . $_ENV['CONVERTKIT_API_PRODUCT_ID'] . '","text":"Buy Now","disable_modal_on_mobile":true} /-->',
			]
		);

		// Load page.
		$I->amOnPage('?p=' . $pageID);

		// Confirm that the block displays without the data-commerce attribute.
		$I->seeElementInDOM('.convertkit-product a');
		$I->dontSeeElementInDOM('.convertkit-product a[data-commerce]');

		// Confirm that clicking the button opens the URL in the same browser tab, and not a modal.
		$I->click('.convertkit-product a');
		$I->waitForElementVisible('body[data-template]');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.4.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
		$I->changeUserAgent($_ENV['TEST_SITE_HTTP_USER_AGENT']);
	}
}
