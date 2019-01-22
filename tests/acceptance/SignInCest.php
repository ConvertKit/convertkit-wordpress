<?php

use Dotenv\Dotenv;

class SignInCest {
	public function _before( AcceptanceTester $I ) {
	}

	// tests
	public function testSettingsPage( AcceptanceTester $I ) {

		$dotenv = new Dotenv( dirname( dirname( __DIR__ ) ) );
		$dotenv->load();

		$ck_api_key    = getenv( 'CK_API_KEY' );
		$ck_api_secret = getenv( 'CK_API_SECRET' );

		$I->cli('plugin activate ConvertKit-WordPress');
		$I->cli('plugin activate contact-form-7');

		$I->loginAsAdmin();
		$I->amOnPluginsPage();
		$I->seePluginActivated( 'convertkit' );
		$I->seePluginActivated( 'contact-form-7' );
		echo 'fart';
		codecept_debug('fart');
		global $wpdb;
		codecept_debug(print_r($wpdb, true));
		die;


		$I->amOnPage( '/wp-admin/options-general.php?page=_wp_convertkit_settings' );
		$I->fillField( "//input[@name='_wp_convertkit_settings[api_key]']", $ck_api_key );
		$I->fillField( "//input[@name='_wp_convertkit_settings[api_secret]']", $ck_api_secret );
		$I->click('Save Changes');
		$I->seeElement('option', ['value' => 'default']);
		$I->seeElement('option', ['value' => '820085']);

		$I->click('Contact Form 7');
		$I->see('ConvertKit Form');
		$I->seeElement('option', ['value' => 'default']);
		$I->seeElement('option', ['value' => '820085']);

		$I->seeOptionIsSelected('form select[id=_wp_convertkit_integration_contactform7_settings_5]', 'None');

		$I->selectOption('form select[id=_wp_convertkit_integration_contactform7_settings_5]', 'Clean form');
		$I->click('Save Changes');

		$I->seeOptionIsSelected('form select[id=_wp_convertkit_integration_contactform7_settings_5]', 'Clean form');
	}
}
