<?php
/**
 * Class Tests
 *
 * @package Convertkit
 */

/**
 * Sample test case.
 */
class CK_Tests extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();

		$dotenv = Dotenv\Dotenv::create( dirname( __DIR__ ) );
		$dotenv->load();
	}
	/**
	 * A single example test.
	 */
	function test_sample() {
		// Replace this with some actual testing code.
		$this->assertTrue( true );

		echo getenv('CK_API_KEY');
	}
}
