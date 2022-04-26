<?php

class ResourceFormsTest extends \Codeception\TestCase\WPTestCase
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
	 * @var 	ConvertKit_Resource_Forms
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
		$this->resource         = new ConvertKit_Resource_Forms();

		// Refresh the resource, which will fetch the data from the API and store them in the option table.
		$result = $this->resource->refresh();
		$this->assertNotInstanceOf(WP_Error::class, $result);
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
		$result = get_option($this->resource->settings_name);
		$this->assertIsArray($result);
		$this->assertArrayHasKey('id', reset($result));
		$this->assertArrayHasKey('name', reset($result));
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
		$this->assertArrayHasKey('name', reset($result));
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