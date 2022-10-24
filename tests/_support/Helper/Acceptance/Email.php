<?php
namespace Helper\Acceptance;

// Define any custom actions related to Select2 interaction that
// would be used across multiple tests.
// These are then available in $I->{yourFunctionName}

class Email extends \Codeception\Module
{
	/**
	 * Generates a unique email address for use in a test, comprising of a prefix,
	 * date + time and PHP version number.
	 *
	 * This ensures that if tests are run in parallel, the same email address
	 * isn't used for two tests across parallel testing runs.
	 *
	 * @since   1.9.6.7
	 */
	public function generateEmailAddress()
	{
		return 'wordpress-' . date( 'Y-m-d-H-i-s' ) . '-php-' . PHP_VERSION_ID . '@convertkit.com';
	}
}
