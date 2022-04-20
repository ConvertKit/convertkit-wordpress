<?php

class ResourcePostsTest extends \Codeception\TestCase\WPTestCase
{
	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	/**
	 * Holds the ConvertKit Settings class.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @var 	ConvertKit_Settings
	 */
	private $settings;

	/**
	 * Holds the ConvertKit Resource class.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @var 	ConvertKit_Resource_Posts
	 */
	private $resource;

	/**
	 * Performs actions before each test.
	 * 
	 * @since 	1.9.7.4
	 */
	public function setUp(): void
	{
		parent::setUp();

		// Store API Key and Secret in Plugin's settings.
		$this->settings = new ConvertKit_Settings();
		update_option($this->settings::SETTINGS_NAME, [
			'api_key'    => $_ENV['CONVERTKIT_API_KEY'],
			'api_secret' => $_ENV['CONVERTKIT_API_SECRET'],
		]);

		// Initialize the resource class we want to test.
		$this->resource = new ConvertKit_Resource_Posts();
		$this->assertNotInstanceOf(WP_Error::class, $this->resource->resources);
	}

	/**
	 * Performs actions after each test.
	 * 
	 * @since 	1.9.6.9
	 */
	public function tearDown(): void
	{
		// Delete API Key, API Secret and Resources from Plugin's settings.
		delete_option($this->settings::SETTINGS_NAME);
		delete_option($this->resource->settings_name);
		delete_option($this->resource->settings_name . '_expiry');
		parent::tearDown();
	}

	/**
	 * Test that the refresh() function performs as expected, storing data in the options table.
	 * 
	 * @since 	1.9.7.4
	 */
	public function testRefresh()
	{
		// Confirm that the data is stored in the options table and includes some expected keys.
		$result = $this->resource->refresh();
		$this->assertIsArray($result);
		$this->assertArrayHasKey('id', reset($result));
		$this->assertArrayHasKey('title', reset($result));
	}

	/**
	 * Test that the expiry timestamp is set and returns the expected value.
	 * 
	 * @since 	1.9.7.4
	 */
	public function testExpiry()
	{
		// Define the expected expiry date based on the resource class' $cache_for setting.
		$expectedExpiryDate = date('Y-m-d', time() + $this->resource->cache_for);

		// Fetch the actual expiry date set when the resource class was initialized.
		$expiryDate = date('Y-m-d', $this->resource->expiry);

		// Confirm both dates match.
		$this->assertEquals($expectedExpiryDate, $expiryDate);
	}

	/**
	 * Test that the get() function performs as expected, storing data in the options table.
	 * 
	 * @since 	1.9.7.4
	 */
	public function testGet()
	{
		// Confirm that the data is fetched from the options table when using get(), and includes some expected keys.
		$result = $this->resource->get();
		$this->assertNotInstanceOf(WP_Error::class, $result);
		$this->assertIsArray($result);
		$this->assertArrayHasKey('id', reset($result));
		$this->assertArrayHasKey('title', reset($result));
	}

	/**
	 * Test that the exist() function performs as expected, storing data in the options table.
	 * 
	 * @since 	1.9.7.4
	 */
	public function testExist()
	{
		// Confirm that the function returns true, because resources exist.
		$result = $this->resource->exist();
		$this->assertSame($result, true);
	}
}