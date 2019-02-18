<?php

class LoginCest
{
	public function tryLogin( FunctionalTester $I ) {
		$I->amOnPage('/');
//		$I->wantTo( 'perform actions and see result' );
		$I->click( 'Login' );
		$I->fillField( 'Username', 'Miles' );
		$I->fillField( 'Password', 'Davis' );
		$I->click('Enter');
		$I->see('Hello, Miles', 'h1');
	}
}

