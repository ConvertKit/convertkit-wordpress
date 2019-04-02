<?php

/**
 * Class SignInCest
 */
class DatabaseCharacterSetCest {
	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws \Codeception\Exception\ModuleException
	 */
	public function _before( AcceptanceTester $I ) {

		$this->ck_api_key    = getenv( 'CK_API_KEY' );
		$this->ck_api_secret = getenv( 'CK_API_SECRET' );

		$I->loginAsAdmin();
	}

	// tests

	/**
	 * @param AcceptanceTester $I
	 */
	public function testSettingsPage( AcceptanceTester $I ) {

		global $wpdb;

		$I->amOnPage( '/wp-admin/options-general.php?page=_wp_convertkit_settings' );
		$I->click( '#submit' );


//		return $wpdb->query( "ALTER TABLE $table CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci" );
//		$I->fillField( "#api_key", $this->ck_api_key );
//		$I->fillField( "#api_secret", $this->ck_api_secret );
//		$I->seeElement( 'option', [ 'value' => 'default' ] );
//		$I->seeElement( 'option', [ 'value' => '820085' ] );
//
//		$I->click( '[href="?page=_wp_convertkit_settings&amp;tab=contactform7"]' );
//		$I->see( 'ConvertKit Form' );
//		$I->seeElement( 'option', [ 'value' => 'default' ] );
//		$I->seeElement( 'option', [ 'value' => '820085' ] );
//
//		$I->selectOption( 'form select[id=_wp_convertkit_integration_contactform7_settings_5]', 'Clean form' );
//		$I->click( '#submit' );
//
//		$I->seeOptionIsSelected( 'form select[id=_wp_convertkit_integration_contactform7_settings_5]', 'Clean form' );
	}

	/**
	 * @param AcceptanceTester $I
	 */
//	public function testJavascriptNotLoaded( AcceptanceTester $I ) {
//
//		$I->amOnPage( '/wp-admin/options-general.php?page=_wp_convertkit_settings' );
//		$I->checkOption('#debug');
//		$I->checkOption('#no_scripts');
//		$I->click('Save Changes');
//
//		$I->amOnPage('/');
//
//		$I->dontSeeInSource('wp-convertkit.js');
//	}
//
//	/**
//	 * @param AcceptanceTester $I
//	 */
//	public function testJavascriptLoaded( AcceptanceTester $I ) {
//
//		$I->amOnPage( '/wp-admin/options-general.php?page=_wp_convertkit_settings' );
//		$I->uncheckOption('#no_scripts');
//		$I->click('Save Changes');
//
//		$I->amOnPage('/');
//
//		$I->seeInSource('wp-convertkit.js');
//	}
}