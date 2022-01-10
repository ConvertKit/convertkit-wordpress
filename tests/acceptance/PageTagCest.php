<?php
/**
 * Tests for ConvertKit Tags on WordPress Pages.
 * 
 * @since 	1.9.6
 */
class PageTagCest
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
	 * Test that 'None' Tag specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPageUsingNoTag(AcceptanceTester $I)
	{
		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-tag');

		// Change Tag to 'None'
		$I->fillSelect2Field($I, '#select2-wp-convertkit-tag-container', 'None');

		// Define a Page Title.
		$I->fillField('#post-title-0', 'ConvertKit: Tag: None');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');

		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Check the value of the Tag field matches the input provided.
		$I->performOn( '.post-publish-panel__postpublish-buttons', function($I) {
			$I->seeOptionIsSelected('#wp-convertkit-tag', 'None');
		});

		// Load the Page on the frontend site.
		$I->amOnPage('/convertkit-tag-none');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the tag parameter is not set to the Tag ID.
		$I->dontSeeInSource('"tag":"' . $_ENV['CONVERTKIT_API_TAG_ID'] . '"');
	}

	/**
	 * Test that the Tag specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPageUsingDefinedTag(AcceptanceTester $I)
	{
		// Navigate to Pages > Add New
		$I->amOnAdminPage('post-new.php?post_type=page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is displayed.
		$I->seeElementInDOM('#wp-convertkit-tag');

		// Change Tag to value specified in the .env file.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-tag-container', $_ENV['CONVERTKIT_API_TAG_NAME']);

		// Define a Page Title.
		$I->fillField('#post-title-0', 'ConvertKit: Tag: Specific');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Check the value of the Tag field matches the input provided.
		$I->performOn( '.post-publish-panel__postpublish-buttons', function($I) {
			$I->seeOptionIsSelected('#wp-convertkit-tag', $_ENV['CONVERTKIT_API_TAG_NAME']);
		});

		// Get Tag ID.
		$tagID = $I->grabValueFrom('#wp-convertkit-tag');

		// Load the Page on the frontend site
		$I->amOnPage('/convertkit-tag-specific');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that the post_has_tag parameter is set to true in the source code.
		$I->seeInSource('"tag":"' . $_ENV['CONVERTKIT_API_TAG_ID'] . '"');
	}
}