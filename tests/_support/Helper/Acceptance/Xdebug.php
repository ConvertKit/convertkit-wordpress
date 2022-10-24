<?php
namespace Helper\Acceptance;

// Define any custom actions related to PHP's Xdebug that
// would be used across multiple tests.
// These are then available in $I->{yourFunctionName}

class Xdebug extends \Codeception\Module {

	/**
	 * Helper method to assert that there are non PHP errors, warnings or notices output
	 *
	 * @since 1.9.6
	 *
	 * @param AcceptanceTester $I Acceptance Tester.
	 */
	public function checkNoWarningsAndNoticesOnScreen( $I ) {
		// Check that no Xdebug errors exist.
		$I->dontSeeElement( '.xdebug-error' );
		$I->dontSeeElement( '.xe-notice' );
	}
}
