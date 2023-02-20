<?php
/**
 * Tests for ConvertKit Settings on WordPress Posts when no API Credentials specified.
 *
 * @since   1.9.6
 */
class PostCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		$I->activateConvertKitPlugin($I);
	}

	/**
	 * Test that the ConvertKit Post Settings displays a message with a link to the Plugin Settings
	 * telling the user to configure their API Credentials, when no API Credentials exist.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPostShowsLinkToPluginSettingsWhenNoAPICredentialsSpecified(AcceptanceTester $I)
	{
		// Navigate to Posts > Add New.
		$I->amOnAdminPage('post-new.php');

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed.
		$I->maybeCloseGutenbergWelcomeModal($I);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that the metabox is displayed.
		$I->seeElementInDOM('#wp-convertkit-meta-box');

		// Check that the Form option is not displayed.
		$I->dontSeeElementInDOM('#wp-convertkit-form');

		// Check that an expected message is displayed.
		$I->seeInSource('To configure the ConvertKit Form / Landing Page to display on this Post, enter your ConvertKit API credentials');

		// Check that a link to the Plugin Settings exists.
		$I->seeInSource('<a href="' . $_ENV['TEST_SITE_WP_URL'] . '/wp-admin/options-general.php?page=_wp_convertkit_settings">Plugin Settings</a>');
	}

	/**
	 * Test that no errors are output when editing or viewing a Post where the Plugin's post level settings
	 * are a string instead of false (no settings) or an array (settings).
	 *
	 * It's unclear how a Page or Post could have a string for its settings, but this covers
	 * https://convertkit.atlassian.net/jira/software/c/projects/T3/boards/27?modal=detail&selectedIssue=T3-173
	 *
	 * @since   2.1.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testPostWithInvalidSettings(AcceptanceTester $I)
	{
		// Setup ConvertKit plugin.
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);

		// Create Post with invalid settings.
		$postID = $I->havePostInDatabase(
			[
				'post_type'  => 'post',
				'post_title' => 'ConvertKit: Post: Invalid Settings',
				'meta_input' => [
					'_wp_convertkit_post_meta' => 'an invalid string setting',
				],
			]
		);

		// Edit the Post.
		$I->amOnAdminPage('post.php?post=' . $postID . '&action=edit');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Load the Post on the frontend site.
		$I->amOnPage('/?p=' . $postID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   1.9.6.7
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
