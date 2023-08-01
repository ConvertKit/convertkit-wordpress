<?php
namespace Helper\Acceptance;

/**
 * Helper methods and actions related to WordPress' Cron system,
 * which are then available using $I->{yourFunctionName}.
 *
 * @since   1.9.6
 */
class WPCron extends \Codeception\Module
{
	/**
	 * Asserts if the given event name is scheduled in WordPress' Cron.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 * @param   string           $name  Event Name.
	 */
	public function seeCronEvent($I, $name)
	{
		$I->assertTrue($this->_cronEventExists($I, $name));
	}

	/**
	 * Asserts if the given event name is not scheduled in WordPress' Cron.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 * @param   string           $name  Event Name.
	 */
	public function dontSeeCronEvent($I, $name)
	{
		$I->assertFalse($this->_cronEventExists($I, $name));
	}

	/**
	 * Runs the given event name using WordPress' Cron, as if
	 * WordPress' Cron system ran the scheduled event.
	 *
	 * Requires the WP-Crontrol Plugin to be installed and activated.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 * @param   string           $name  Event Name.
	 */
	public function runCronEvent($I, $name)
	{
		// List cron event in WP-Crontrol Plugin.
		$I->amOnAdminPage('tools.php?page=crontrol_admin_manage_page&s=' . $name);

		// Hover mouse over event's name.
		$I->moveMouseOver('#the-list tr');

		// Run the event.
		$I->click('Run now');
	}

	/**
	 * Returns whether the given event name is scheduled in WordPress' Cron.
	 *
	 * @since   2.2.8
	 *
	 * @param   AcceptanceTester $I     AcceptanceTester.
	 * @param   string           $name  Event Name.
	 */
	private function _cronEventExists($I, $name)
	{
		$cron   = $I->grabOptionFromDatabase('cron');
		$exists = false;

		// Iterate through the array until a match is found.
		foreach ( $cron as $event ) {
			// Skip if the event is not an array; it's not really an event.
			if ( ! is_array( $event ) ) {
				continue;
			}

			if ( array_key_exists( $name, $event ) ) {
				$exists = true;
				break;
			}
		}

		return $exists;
	}
}
