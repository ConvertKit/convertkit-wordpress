<?php
/**
 * Tests edge cases when upgrading between specific ConvertKit Plugin versions.
 * 
 * @since 	1.9.6.4
 */
class UpgradePathsCest
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
		$I->enableDebugLog($I);
	}

	/**
	 * Check for undefined index errors for a Post when upgrading from 1.4.6 or earlier to 1.4.7 or later.
	 * 
	 * @since 	1.9.6.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testUndefinedIndexForPost(AcceptanceTester $I)
	{
		// Create a Post with Post Meta that does not include landing_page and tag keys,
		// mirroring how 1.4.6 and earlier of the Plugin worked.
		$postID = $I->havePageInDatabase([
			'post_type' => 'post',
			'post_status' => 'publish',
			'post_title' => 'ConvertKit: Post: 1.4.6',
			'post_name' => 'convertkit-post-1-4-6',
			'meta_input' => [
				// 1.4.6 and earlier wouldn't set a landing_page or tag meta keys if no values were specified
				// in the Meta Box.
				'_wp_convertkit_post_meta' => [
					'form'         => '-1',
				],
			],
		]);

		// Load the Post on the frontend site.
		$I->amOnPage('convertkit-post-1-4-6');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Check for undefined index errors for a Page when upgrading from 1.4.6 or earlier to 1.4.7 or later.
	 * 
	 * @since 	1.9.6.4
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testUndefinedIndexForPage(AcceptanceTester $I)
	{
		// Create a Page with Post Meta that does not include landing_page and tag keys,
		// mirroring how 1.4.6 and earlier of the Plugin worked.
		$postID = $I->havePageInDatabase([
			'post_type' => 'post',
			'post_status' => 'publish',
			'post_title' => 'ConvertKit: Page: 1.4.6',
			'post_name' => 'convertkit-page-1-4-6',
			'meta_input' => [
				// 1.4.6 and earlier wouldn't set a landing_page or tag meta keys if no values were specified
				// in the Meta Box.
				'_wp_convertkit_post_meta' => [
					'form'         => '-1',
				],
			],
		]);

		// Load the Post on the frontend site.
		$I->amOnPage('convertkit-page-1-4-6');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 * 
	 * @since 	1.9.6.7
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}