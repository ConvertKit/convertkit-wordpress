<?php
/**
 * Tests Refresh Resource buttons, which are displayed next to settings fields
 * across Page/Post editing, Bulk/Quick edit and Category editing.
 *
 * @since   1.9.8.0
 */
class RefreshResourcesButtonCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate and Setup ConvertKit plugin.
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Enable Restrict Content, so the Products refresh button can be tested.
		$I->setupConvertKitPluginRestrictContent(
			$I,
			[
				'enabled' => true,
			]
		);
	}

	/**
	 * Test that the refresh buttons for Forms, Landing Pages, Tags and Restrict Content works when adding a new Page.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRefreshResourcesOnPage(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY'], $_ENV['CONVERTKIT_API_SECRET']);

		// Navigate to Pages > Add New.
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed.
		$I->maybeCloseGutenbergWelcomeModal($I);

		// Click the Forms refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="forms"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="forms"]:not(:disabled)');

		// Check the order of the Form resources are alphabetical, with Default and None options prepending the Forms.
		$I->checkSelectFormOptionOrder(
			$I,
			'#wp-convertkit-form',
			[
				'Default',
				'None',
			]
		);

		// Change resource to value specified in the .env file, which should now be available.
		// If the expected dropdown value does not exist in the Select2 field, this will fail the test.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Click the Landing Pages refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="landing_pages"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="landing_pages"]:not(:disabled)');

		// Check the order of the Landing Page resources are alphabetical, with the None option prepending the Landing Pages.
		$I->checkSelectLandingPageOptionOrder(
			$I,
			'#wp-convertkit-landing_page',
			[
				'None',
			]
		);

		// Change resource to value specified in the .env file, which should now be available.
		// If the expected dropdown value does not exist in the Select2 field, this will fail the test.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-landing_page-container', $_ENV['CONVERTKIT_API_LANDING_PAGE_NAME']);

		// Click the Tags refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="tags"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="tags"]:not(:disabled)');

		// Check the order of the Tag resources are alphabetical, with the None option prepending the Tags.
		$I->checkSelectTagOptionOrder(
			$I,
			'#wp-convertkit-tag',
			[
				'None',
			]
		);

		// Change resource to value specified in the .env file, which should now be available.
		// If the expected dropdown value does not exist in the Select2 field, this will fail the test.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-tag-container', $_ENV['CONVERTKIT_API_TAG_NAME']);

		// Click the Restrict Content refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="products"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="products"]:not(:disabled)');

		// Change resource to value specified in the .env file, which should now be available.
		// If the expected dropdown value does not exist in the Select2 field, this will fail the test.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-restrict_content-container', $_ENV['CONVERTKIT_API_PRODUCT_NAME']);
	}

	/**
	 * Test that the refresh buttons for Forms and Tags works when Quick Editing a Page.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRefreshResourcesOnQuickEdit(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY'], $_ENV['CONVERTKIT_API_SECRET']);

		// Programmatically create a Page.
		$pageID = $I->havePostInDatabase(
			[
				'post_type'  => 'page',
				'post_title' => 'ConvertKit: Page: Refresh Resources: Quick Edit',
			]
		);

		// Open Quick Edit form forthe Page in the Pages WP_List_Table.
		$I->openQuickEdit($I, 'page', $pageID);

		// Click the Forms refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="forms"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="forms"]:not(:disabled)');

		// Check the order of the Form resources are alphabetical, with Default and None options prepending the Forms.
		$I->checkSelectFormOptionOrder(
			$I,
			'#wp-convertkit-quick-edit-form',
			[
				'Default',
				'None',
			]
		);

		// Change resource to value specified in the .env file, which should now be available.
		// If the expected dropdown value does not exist, this will fail the test.
		$I->selectOption('#wp-convertkit-quick-edit-form', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Click the Tags refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="tags"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="tags"]:not(:disabled)');

		// Check the order of the Tag resources are alphabetical, with the None option prepending the Tags.
		$I->checkSelectTagOptionOrder(
			$I,
			'#wp-convertkit-quick-edit-tag',
			[
				'None',
			]
		);

		// Change resource to value specified in the .env file, which should now be available.
		// If the expected dropdown value does not exist, this will fail the test.
		$I->selectOption('#wp-convertkit-quick-edit-tag', $_ENV['CONVERTKIT_API_TAG_NAME']);

		// Click the Products refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="products"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="products"]:not(:disabled)');

		// Change resource to value specified in the .env file, which should now be available.
		// If the expected dropdown value does not exist, this will fail the test.
		$I->selectOption('#wp-convertkit-quick-edit-restrict_content', $_ENV['CONVERTKIT_API_PRODUCT_NAME']);
	}

	/**
	 * Test that the refresh buttons for Forms and Tags works when Bulk Editing Pages.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRefreshResourcesOnBulkEdit(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY'], $_ENV['CONVERTKIT_API_SECRET']);

		// Programmatically create two Pages.
		$pageIDs = array(
			$I->havePostInDatabase(
				[
					'post_type'  => 'page',
					'post_title' => 'ConvertKit: Page: Refresh Resources: Bulk Edit #1',
				]
			),
			$I->havePostInDatabase(
				[
					'post_type'  => 'page',
					'post_title' => 'ConvertKit: Page: Refresh Resources: Bulk Edit #2',
				]
			),
		);

		// Open Bulk Edit form for the Pages in the Pages WP_List_Table.
		$I->openBulkEdit($I, 'page', $pageIDs);

		// Click the Forms refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="forms"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="forms"]:not(:disabled)');

		// Check the order of the Form resources are alphabetical, with No Change, Default and None options prepending the Forms.
		$I->checkSelectFormOptionOrder(
			$I,
			'#wp-convertkit-bulk-edit-form',
			[
				'— No Change —',
				'Default',
				'None',
			]
		);

		// Change resource to value specified in the .env file, which should now be available.
		// If the expected dropdown value does not exist, this will fail the test.
		$I->selectOption('#wp-convertkit-bulk-edit-form', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Click the Tags refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="tags"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="tags"]:not(:disabled)');

		// Check the order of the Tag resources are alphabetical, with the No Chage and None options prepending the Tags.
		$I->checkSelectTagOptionOrder(
			$I,
			'#wp-convertkit-bulk-edit-tag',
			[
				'— No Change —',
				'None',
			]
		);

		// Change resource to value specified in the .env file, which should now be available.
		// If the expected dropdown value does not exist, this will fail the test.
		$I->selectOption('#wp-convertkit-bulk-edit-tag', $_ENV['CONVERTKIT_API_TAG_NAME']);

		// Click the Products refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="products"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="products"]:not(:disabled)');

		// Change resource to value specified in the .env file, which should now be available.
		// If the expected dropdown value does not exist, this will fail the test.
		$I->selectOption('#wp-convertkit-bulk-edit-restrict_content', $_ENV['CONVERTKIT_API_PRODUCT_NAME']);
	}

	/**
	 * Test that the refresh button for Forms works when adding a Category.
	 *
	 * @since   2.0.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRefreshResourcesOnAddCategory(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY'], $_ENV['CONVERTKIT_API_SECRET']);

		// Navigate to Posts > Categories.
		$I->amOnAdminPage('edit-tags.php?taxonomy=category');

		// Click the Forms refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="forms"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="forms"]:not(:disabled)');

		// Check the order of the Form resources are alphabetical, with the Default option prepending the Forms.
		$I->checkSelectFormOptionOrder(
			$I,
			'#wp-convertkit-form',
			[
				'Default',
			]
		);

		// Change resource to value specified in the .env file, which should now be available.
		// If the expected dropdown value does not exist in the Select2 field, this will fail the test.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);
	}

	/**
	 * Test that the refresh button for Forms works when editing a Category.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRefreshResourcesOnEditCategory(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin.
		$I->setupConvertKitPlugin($I, $_ENV['CONVERTKIT_API_KEY'], $_ENV['CONVERTKIT_API_SECRET']);

		// Create Category.
		$termID = $I->haveTermInDatabase( 'ConvertKit Refresh Resources', 'category' );
		$termID = $termID[0];

		// Edit the Term.
		$I->amOnAdminPage('term.php?taxonomy=category&tag_ID=' . $termID);

		// Click the Forms refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="forms"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="forms"]:not(:disabled)');

		// Check the order of the Form resources are alphabetical, with the Default option prepending the Forms.
		$I->checkSelectFormOptionOrder(
			$I,
			'#wp-convertkit-form',
			[
				'Default',
			]
		);

		// Change resource to value specified in the .env file, which should now be available.
		// If the expected dropdown value does not exist in the Select2 field, this will fail the test.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);
	}

	/**
	 * Test that the refresh button triggers an error message when the AJAX request fails,
	 * or the ConvertKit API returns an error, when adding a Page using the Gutenberg editor.
	 *
	 * @since   1.9.8.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRefreshResourcesErrorNoticeOnPage(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with invalid API credentials, so that the AJAX request returns an error.
		$I->setupConvertKitPlugin($I, 'fakeApiKey', 'fakeApiSecret', '', '', '');

		// Navigate to Pages > Add New.
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed.
		$I->maybeCloseGutenbergWelcomeModal($I);

		// Click the Forms refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="forms"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="forms"]:not(:disabled)');

		// Confirm that an error notification is displayed on screen, with the expected error message.
		$I->seeElementInDOM('div.components-notice-list div.is-error');
		$I->see('API Key not valid');

		// Confirm that the notice is dismissible.
		$I->click('div.components-notice-list div.is-error button.components-notice__dismiss');
		$I->wait(1);
		$I->dontSeeElementInDOM('div.components-notice-list div.is-error');
	}

	/**
	 * Test that the refresh button triggers an error message when the AJAX request fails,
	 * or the ConvertKit API returns an error, when adding a Page using the Classic Editor.
	 *
	 * @since   1.9.8.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRefreshResourcesErrorNoticeOnPageClassicEditor(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with invalid API credentials, so that the AJAX request returns an error.
		$I->setupConvertKitPlugin($I, 'fakeApiKey', 'fakeApiSecret', '', '', '');

		// Add a Page using the Classic Editor.
		$I->addClassicEditorPage($I, 'page', 'ConvertKit: Page: Refresh Resources: Classic Editor' );

		// Click the Forms refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="forms"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="forms"]:not(:disabled)');

		// Confirm that an error notification is displayed on screen, with the expected error message.
		$I->seeElementInDOM('div.convertkit-error');
		$I->see('API Key not valid');

		// Confirm that the notice is dismissible.
		$I->click('div.convertkit-error button.notice-dismiss');
		$I->wait(1);
		$I->dontSeeElementInDOM('div.convertkit-error');
	}

	/**
	 * Test that the refresh button triggers an error message when the AJAX request fails,
	 * or the ConvertKit API returns an error, when using the Quick Edit functionality.
	 *
	 * @since   1.9.8.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRefreshResourcesErrorNoticeOnQuickEdit(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with invalid API credentials, so that the AJAX request returns an error.
		$I->setupConvertKitPlugin($I, 'fakeApiKey', 'fakeApiSecret', '', '', '');

		// Programmatically create a Page.
		$pageID = $I->havePostInDatabase(
			[
				'post_type'  => 'page',
				'post_title' => 'ConvertKit: Page: Refresh Resources: Quick Edit',
			]
		);

		// Open Quick Edit form forthe Page in the Pages WP_List_Table.
		$I->openQuickEdit($I, 'page', $pageID);

		// Click the Forms refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="forms"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="forms"]:not(:disabled)', 5);

		// Confirm that an error notification is displayed on screen, with the expected error message.
		$I->seeElementInDOM('div.convertkit-error');
		$I->see('API Key not valid');

		// Confirm that the notice is dismissible.
		$I->click('div.convertkit-error button.notice-dismiss');
		$I->wait(1);
		$I->dontSeeElementInDOM('div.convertkit-error');
	}

	/**
	 * Test that the refresh button triggers an error message when the AJAX request fails,
	 * or the ConvertKit API returns an error, when using the Bulk Edit functionality.
	 *
	 * @since   1.9.8.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRefreshResourcesErrorNoticeOnBulkEdit(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with invalid API credentials, so that the AJAX request returns an error.
		$I->setupConvertKitPlugin($I, 'fakeApiKey', 'fakeApiSecret', '', '', '');

		// Programmatically create two Pages.
		$pageIDs = array(
			$I->havePostInDatabase(
				[
					'post_type'  => 'page',
					'post_title' => 'ConvertKit: Page: Refresh Resources: Bulk Edit #1',
				]
			),
			$I->havePostInDatabase(
				[
					'post_type'  => 'page',
					'post_title' => 'ConvertKit: Page: Refresh Resources: Bulk Edit #2',
				]
			),
		);

		// Open Bulk Edit form for the Pages in the Pages WP_List_Table.
		$I->openBulkEdit($I, 'page', $pageIDs);

		// Click the Forms refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="forms"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="forms"]:not(:disabled)');

		// Confirm that an error notification is displayed on screen, with the expected error message.
		$I->seeElementInDOM('div.convertkit-error');
		$I->see('API Key not valid');

		// Confirm that the notice is dismissible.
		$I->click('div.convertkit-error button.notice-dismiss');
		$I->wait(1);
		$I->dontSeeElementInDOM('div.convertkit-error');
	}

	/**
	 * Test that the refresh button triggers an error message when the AJAX request fails,
	 * or the ConvertKit API returns an error, when adding a Category.
	 *
	 * @since   2.0.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRefreshResourcesErrorNoticeOnAddCategory(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with invalid API credentials, so that the AJAX request returns an error.
		$I->setupConvertKitPlugin($I, 'fakeApiKey', 'fakeApiSecret', '', '', '');

		// Navigate to Posts > Categories.
		$I->amOnAdminPage('edit-tags.php?taxonomy=category');

		// Click the Forms refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="forms"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="forms"]:not(:disabled)');

		// Confirm that an error notification is displayed on screen, with the expected error message.
		$I->seeElementInDOM('div.convertkit-error');
		$I->see('API Key not valid');

		// Confirm that the notice is dismissible.
		$I->click('div.convertkit-error button.notice-dismiss');
		$I->wait(1);
		$I->dontSeeElementInDOM('div.convertkit-error');
	}

	/**
	 * Test that the refresh button triggers an error message when the AJAX request fails,
	 * or the ConvertKit API returns an error, when editing a Category.
	 *
	 * @since   1.9.8.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testRefreshResourcesErrorNoticeOnEditCategory(AcceptanceTester $I)
	{
		// Setup ConvertKit Plugin with invalid API credentials, so that the AJAX request returns an error.
		$I->setupConvertKitPlugin($I, 'fakeApiKey', 'fakeApiSecret', '', '', '');

		// Create Category.
		$termID = $I->haveTermInDatabase( 'ConvertKit Refresh Resources', 'category' );
		$termID = $termID[0];

		// Edit the Term.
		$I->amOnAdminPage('term.php?taxonomy=category&tag_ID=' . $termID);

		// Click the Forms refresh button.
		$I->click('button.wp-convertkit-refresh-resources[data-resource="forms"]');

		// Wait for button to change its state from disabled.
		$I->waitForElementVisible('button.wp-convertkit-refresh-resources[data-resource="forms"]:not(:disabled)');

		// Confirm that an error notification is displayed on screen, with the expected error message.
		$I->seeElementInDOM('div.convertkit-error');
		$I->see('API Key not valid');

		// Confirm that the notice is dismissible.
		$I->click('div.convertkit-error button.notice-dismiss');
		$I->wait(1);
		$I->dontSeeElementInDOM('div.convertkit-error');
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateThirdPartyPlugin($I, 'classic-editor');
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
