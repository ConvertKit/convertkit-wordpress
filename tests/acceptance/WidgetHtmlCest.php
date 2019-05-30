<?php

class WidgetHtmlCest
{
    public function _before(AcceptanceTester $I)
    {
    	$I->useTheme('twentysixteen');
    	$I->loginAsAdmin();
    }

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
    	$I->amOnPage('/wp-admin/widgets.php');
    	$I->click('ConvertKit Form');
    	$I->click('Sidebar');
    	$I->click('Add Widget');
	    $I->wait(1);
	    $I->havePageInDatabase(
		    [
			    'post_name' => 'sidebar',
		    ]
	    );

	    $I->amOnPage( '/sidebar' );
	    $I->seeElement('.widget_convertkit_form');
    }
}
