<?php

use Dotenv\Dotenv;

/**
 * Class ChangeCategoryFormCest
 */
class ChangeCategoryFormCest {
	/**
	 * @param AcceptanceTester $I
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

	/**
	 * @param AcceptanceTester $I
	 */
	public function testChangeCategoryForm( AcceptanceTester $I ) {
		$I->amOnPage( '/wp-admin/term.php?taxonomy=category&tag_ID=1' );
		$I->selectOption( 'form select[id=ck_default_form]', 'Clean form' );
		$I->click( 'Update', '.button' );
		$I->see('Clean form');

		$I->selectOption( 'form select[id=ck_default_form]', 'None' );
		$I->click( 'Update', '.button' );
		$I->seeOptionIsSelected('form select[id=ck_default_form]', 'None');
		$I->see('None');
	}
}