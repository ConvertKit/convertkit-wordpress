<?php
/**
 * Tests for ConvertKit Landing Pages on WordPress Posts.
 * 
 * @since 	1.9.6.4
 */
class PostLandingPageCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 * 
	 * @since 	1.9.6.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate and Setup ConvertKit plugin
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);

		// Navigate to Post > Add New
		$I->amOnAdminPage('post-new.php');

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed
		$I->maybeCloseGutenbergWelcomeModal($I);
	}

	/**
	 * Test that no Landing Page option is displayedin the Plugin Settings when
	 * creating and viewing a new WordPress Post, and that no attempt to check
	 * for a Landing Page is made when viewing a Post.
	 * 
	 * @since 	1.9.6.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testAddNewPostDoesNotDisplayLandingPageOption(AcceptanceTester $I)
	{
		// Navigate to Post > Add New
		$I->amOnAdminPage('post-new.php');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that no Landing Page option is displayed.
		$I->dontSeeElementInDOM('#wp-convertkit-landing_page');

		// Define a Post Title.
		$I->fillField('#post-title-0', 'ConvertKit: Post: Landing Page');

		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');
		
		// When the pre-publish panel displays, click Publish again.
		$I->performOn('.editor-post-publish-panel__prepublish', function($I) {
			$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');	
		});

		// Check the value of the Form field matches the input provided.
		$I->performOn( '.post-publish-panel__postpublish-buttons', function($I) {
			$I->seeOptionIsSelected('#wp-convertkit-form', 'Default');
		});

		// Load the Post on the frontend site.
		$I->amOnPage('/convertkit-post-landing-page');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}
}