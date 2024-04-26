<?php
/**
 * Tests for the ConvertKit_API class.
 *
 * @since   2.0.8
 */
class APITest extends \Codeception\TestCase\WPTestCase
{
	/**
	 * The testing implementation.
	 *
	 * @var \WpunitTester.
	 */
	protected $tester;

	/**
	 * Holds the ConvertKit API class.
	 *
	 * @since   2.0.8
	 *
	 * @var     ConvertKit_API
	 */
	private $api;

	/**
	 * Performs actions before each test.
	 *
	 * @since   2.0.8
	 */
	public function setUp(): void
	{
		parent::setUp();

		// Activate Plugin, to include the Plugin's constants in tests.
		activate_plugins('convertkit/wp-convertkit.php');

		// Initialize the classes we want to test.
		$this->api = new ConvertKit_API(
			$_ENV['CONVERTKIT_OAUTH_CLIENT_ID'],
			$_ENV['CONVERTKIT_OAUTH_REDIRECT_URI'],
			$_ENV['CONVERTKIT_OAUTH_ACCESS_TOKEN'],
			$_ENV['CONVERTKIT_OAUTH_REFRESH_TOKEN']
		);
	}

	/**
	 * Performs actions after each test.
	 *
	 * @since   2.0.8
	 */
	public function tearDown(): void
	{
		// Destroy the classes we tested.
		unset($this->api);

		parent::tearDown();
	}

	/**
	 * Test that the User Agent string is in the expected format and
	 * includes the Plugin's name and version number.
	 *
	 * @since   2.0.8
	 */
	public function testUserAgent()
	{
		// When an API call is made, inspect the user-agent argument.
		add_filter(
			'http_request_args',
			function($args, $url) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter
				$this->assertStringContainsString(
					CONVERTKIT_PLUGIN_NAME . '/' . CONVERTKIT_PLUGIN_VERSION,
					$args['user-agent']
				);
				return $args;
			},
			10,
			2
		);

		// Perform a request.
		$result = $this->api->get_account();
	}
}
