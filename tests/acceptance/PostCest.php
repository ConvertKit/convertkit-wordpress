<?php
/**
 * Tests for ConvertKit Settings on WordPress Posts when no API Credentials specified.
 * 
 * @since 	1.9.6
 */
class PostCest
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
    	$I->activateConvertKitPlugin($I);
    }

    /**
	 * Test that the ConvertKit Post Settings displays a message with a link to the Plugin Settings
	 * telling the user to configure their API Credentials, when no API Credentials exist.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
    public function testAddNewPostShowsLinkToPluginSettingsWhenNoAPICredentialsSpecified(AcceptanceTester $I)
    {
    	// Navigate to Posts > Add New
        $I->amOnAdminPage('post-new.php');

        // Check that no PHP warnings or notices were output.
    	$I->checkNoWarningsAndNoticesOnScreen($I);

    	// Check that the metabox is displayed.
    	$I->seeElementInDOM('#wp-convertkit-meta-box');

    	// Check that the Form option is not displayed.
    	$I->dontSeeElementInDOM('#wp-convertkit-form');

    	// Check that an expected message is displayed.
    	$I->seeInSource('To configure the ConvertKit Form / Landing Page to display on this Post, enter your ConvertKit API credentials');

    	// Check that a Plugin Settings link exists and loads the Plugin Settings screen.
    	$I->click('#wp-convertkit-meta-box a');
    	$I->seeElement('#api_key');
    }
}