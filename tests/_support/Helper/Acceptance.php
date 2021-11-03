<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class Acceptance extends \Codeception\Module
{
	/**
     * Helper method to assert that there are non PHP errors, warnings or notices output
     * 
     * @since 	1.0.0
	 */
    public function checkNoWarningsAndNoticesOnScreen($I)
    {
    	// Check that the <body> class does not have a php-error class, which indicates a suppressed PHP function call error.
        $I->dontSeeElement('body.php-error');

        // Check that no Xdebug errors exist.
        $I->dontSeeElement('.xdebug-error');
        $I->dontSeeElement('.xe-notice');
    }

    /**
     * Helper method to assert that the field's value contains the given value.
     * 
     * @since 	1.0.0
     */
    public function seeFieldContains($I, $element, $value)
    {
    	$this->assertNotFalse(strpos($I->grabValueFrom($element), $value));
    }
}
