<?php

use Dotenv\Dotenv;

/**
 * Class SignInCest
 */
class SignInCest {
	/**
	 * @param AcceptanceTester $I
	 *
	 * @throws \Codeception\Exception\ModuleException
	 */
	public function _before( AcceptanceTester $I ) {

		$dotenv = new Dotenv( dirname( dirname( __DIR__ ) ) );
		$dotenv->load();

		$this->ck_api_key    = getenv( 'CK_API_KEY' );
		$this->ck_api_secret = getenv( 'CK_API_SECRET' );


		$I->cli('plugin activate ConvertKit-WordPress');
		$I->cli('plugin activate contact-form-7');
		$I->loginAsAdmin();
	}

	// tests

	/**
	 * @param AcceptanceTester $I
	 */
	public function testSettingsPage( AcceptanceTester $I ) {

		$I->loginAsAdmin();
		$I->amOnPluginsPage();

		$I->amOnPage( '/wp-admin/options-general.php?page=_wp_convertkit_settings' );
		$I->fillField( "//input[@name='_wp_convertkit_settings[api_key]']", $this->ck_api_key );
		$I->fillField( "//input[@name='_wp_convertkit_settings[api_secret]']", $this->ck_api_secret );
		$I->click( 'Save Changes' );
		$I->seeElement( 'option', [ 'value' => 'default' ] );
		$I->seeElement( 'option', [ 'value' => '820085' ] );

		$I->click( 'Contact Form 7' );
		$I->see( 'ConvertKit Form' );
		$I->seeElement( 'option', [ 'value' => 'default' ] );
		$I->seeElement( 'option', [ 'value' => '820085' ] );

		$I->selectOption( 'form select[id=_wp_convertkit_integration_contactform7_settings_5]', 'Clean form' );
		$I->click( 'Save Changes' );

		$I->seeOptionIsSelected( 'form select[id=_wp_convertkit_integration_contactform7_settings_5]', 'Clean form' );
	}

	/**
	 * @param AcceptanceTester $I
	 */
	public function testJavascriptNotLoaded( AcceptanceTester $I ) {

		$I->amOnPage( '/wp-admin/options-general.php?page=_wp_convertkit_settings' );
		$I->checkOption('#debug');
		$I->checkOption('#no_scripts');
		$I->click('Save Changes');

		$I->amOnPage('/');

		$I->dontSeeInSource('wp-convertkit.js');
	}

	/**
	 * @param AcceptanceTester $I
	 */
	public function testJavascriptLoaded( AcceptanceTester $I ) {

		$I->amOnPage( '/wp-admin/options-general.php?page=_wp_convertkit_settings' );
		$I->uncheckOption('#no_scripts');
		$I->click('Save Changes');

		$I->amOnPage('/');

		$I->seeInSource('wp-convertkit.js');
	}
}