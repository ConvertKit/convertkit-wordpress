<?php
/**
 * Tests for ConvertKit Forms integration with WishList Member.
 * 
 * @since 	1.9.6
 */
class WishListMemberCest
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
		$I->activateThirdPartyPlugin($I, 'wishlist-member');
		$I->setupConvertKitPlugin($I);
		$I->enableDebugLog($I);
		$I->wait(2);
		$I->setupWishListMemberPlugin($I);
	}

	/**
	 * Test that saving a WishList Member Level to ConvertKit Form Mapping works.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 */
	public function testSettingsWishListMemberLevelToConvertKitFormMapping(AcceptanceTester $I)
	{
		// Get WishList Member Level ID defined.
		$wlmLevelID = $this->_getWishListMemberLevelID($I);

		// Define email address for this test.
		$emailAddress = $I->generateEmailAddress();

		// Create a test WordPress User.
		$userID = $this->_createUser($I, $emailAddress);

		// Load WishList Member Plugin Settings
		$I->amOnAdminPage('options-general.php?page=_wp_convertkit_settings&tab=wishlist-member');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check that a Form Mapping option is displayed.
		$I->seeElementInDOM('#_wp_convertkit_integration_wishlistmember_settings_' . $wlmLevelID . '_form');

		// Change Form to value specified in the .env file.
		$I->selectOption('#_wp_convertkit_integration_wishlistmember_settings_' . $wlmLevelID . '_form', $_ENV['CONVERTKIT_API_THIRD_PARTY_INTEGRATIONS_FORM_NAME']);

		// Save Changes.
		$I->click('Save Changes');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Check the value of the Form field matches the input provided.
		$I->seeOptionIsSelected('#_wp_convertkit_integration_wishlistmember_settings_' . $wlmLevelID . '_form', $_ENV['CONVERTKIT_API_THIRD_PARTY_INTEGRATIONS_FORM_NAME']);

		// Edit the Test User.
		$I->amOnAdminPage('user-edit.php?user_id=' . $userID . '&wp_http_referer=%2Fwp-admin%2Fusers.php');

		// Map the User to the Bronze WLM Level.
		$I->checkOption('#WishListMemberUserProfile input[value="'. $wlmLevelID . '"]');
		
		// Save Changes.
		$I->click('Update Member Profile');

		// Confirm that the User is still assigned to the Bronze WLM Level.
		$I->seeCheckboxIsChecked('#WishListMemberUserProfile input[value="'. $wlmLevelID . '"]');
		
		// Confirm that the email address was added to ConvertKit.
		$I->apiCheckSubscriberExists($I, $emailAddress);
	}

	/**
	 * Returns the WishList Member Level ID created when setupWishListMemberPlugin() was called.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 	Tester
	 * @return 	int 					Level ID
	 */
	private function _getWishListMemberLevelID(AcceptanceTester $I)
	{
		$table = $I->grabPrefixedTableNameFor('wlm_options');
		$wlmLevels = $I->grabAllFromDatabase($table, 'option_value', ['option_name' => 'wpm_levels']);
		$wlmLevels = unserialize( $wlmLevels[0]['option_value'] );
		return array_key_first( $wlmLevels );
	}

	/**
	 * Creates a WordPress User, returning their User ID.
	 * 
	 * @since 	1.9.6
	 * 
	 * @param 	AcceptanceTester 	$I 				Tester
	 * @param 	string 				$emailAddress 	Email Address
	 * @return 	int 								User ID
	 */
	private function _createUser(AcceptanceTester $I, $emailAddress)
	{
		return $I->haveUserInDatabase('wlm_test_user', 'subscriber', [
			'user_email' => $emailAddress,
			'first_name' => 'Test',
			'last_name' => 'User',
			'display_name' => 'Test User',
		]);
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
		$I->deactivateThirdPartyPlugin($I, 'wishlist-member');
	}
}