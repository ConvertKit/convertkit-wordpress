<?php

class LogFileWarningCest
{
    public function _before(AcceptanceTester $I)
    {
    	$I->loginAsAdmin();
    }

	/**
	 * @param AcceptanceTester $I
	 * @param \Codeception\Scenario $scenario
	 *
	 * When visiting tools tab of plugin settings page, no PHP warning should be shown,
	 * even if the log file does not already exist.
	 */
	public function testWarningIsNotShown(AcceptanceTester $I, \Codeception\Scenario $scenario)
	{
		$I->wantTo( 'Test that visiting the tools tab of the plugin settings page does not result in a PHP warning being shown.' );

		/*
		 * Turn on error reporting and display, so even if wp-config.php somehow has them turned off, we'll
		 * still see them if the test fails
		 */
		error_reporting( E_ALL );
		ini_set( 'display_errors', 1 );

		/**
		 * Ensure the log file does not already exist
		 */
		$log_file = trailingslashit( CONVERTKIT_PLUGIN_PATH ) . 'log.txt';

		if ( file_exists( $log_file ) ) {
			unlink( $log_file );
		}

		$I->amOnPage( '/wp-admin/options-general.php?page=_wp_convertkit_settings&tab=tools' );
		$I->dontSee( 'No such file or directory' );
	}
}
