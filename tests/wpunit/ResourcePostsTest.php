<?php
/**
 * Tests for the ConvertKit_Resource_Posts class.
 * 
 * @since 1.9.7.4
 */
class ResourcePostsTest extends \Codeception\TestCase\WPTestCase
{
    /**
     * The test object.
     *
     * @var \WpunitTester
     */
    protected $tester;

    /**
     * Holds the ConvertKit Settings class.
     * 
     * @since 1.9.7.4
     * 
     * @var ConvertKit_Settings
     */
    private $settings;

    /**
     * Holds the ConvertKit Resource class.
     * 
     * @since 1.9.7.4
     * 
     * @var ConvertKit_Resource_Posts
     */
    private $resource;

    /**
     * Performs actions before each test.
     * 
     * @since 1.9.7.4
     */
    public function setUp(): void
    {
        parent::setUp();

        // Activate Plugin.
        activate_plugins('convertkit/wp-convertkit.php');

        // Store API Key and Secret in Plugin's settings.
        $this->settings = new ConvertKit_Settings();
        update_option(
            $this->settings::SETTINGS_NAME, [
            'api_key'    => $_ENV['CONVERTKIT_API_KEY'],
            'api_secret' => $_ENV['CONVERTKIT_API_SECRET'],
            ]
        );

        // Initialize the resource class we want to test.
        $this->resource = new ConvertKit_Resource_Posts();

        // Confirm initialization didn't result in an error.
        $this->assertNotInstanceOf(WP_Error::class, $this->resource->resources);
    }

    /**
     * Performs actions after each test.
     * 
     * @since 1.9.6.9
     */
    public function tearDown(): void
    {
        // Delete API Key, API Secret and Resources from Plugin's settings.
        delete_option($this->settings::SETTINGS_NAME);
        delete_option($this->resource->settings_name);
        delete_option($this->resource->settings_name . '_last_queried');

        // Destroy the resource class we tested.
        unset($this->resource);

        // Deactivate Plugin.
        deactivate_plugins('convertkit/wp-convertkit.php');

        parent::tearDown();
    }

    /**
     * Test that the WordPress Cron event for this resource was created with the expected name,
     * matching the expected schedule as defined in the Resource's class.
     * 
     * @since 1.9.7.4
     */
    public function testCronEventCreatedOnPluginActivation()
    {
        // Confirm the event was scheduled.
        $this->assertEquals(
            wp_get_schedule('convertkit_resource_refresh_' . $this->resource->type),
            $this->resource->wp_cron_schedule
        );
    }

    /**
     * Test that the WordPress Cron event for this resource was created with the expected name,
     * matching the expected schedule as defined in the Resource's class, when updating
     * from an earlier version of the Plugin to 1.9.7.4 or higher.
     * 
     * @since 1.9.7.4
     */
    public function testCronEventCreatedOnPluginUpdate()
    {
        // Delete scheduled event.
        $this->resource->unschedule_cron_event();

        // Confirm scheduled event does not exist.
        $this->assertFalse(wp_get_schedule('convertkit_resource_refresh_' . $this->resource->type));

        // Set Plugin version number in options table to < 1.9.7.4.
        update_option('convertkit_version', '1.9.7.2');

        // Run the update action as WordPress would when updating the Plugin to a newer version.
        $convertkit = WP_ConvertKit();
        $convertkit->update();

        // Confirm the Plugin version number matches the current version.
        $this->assertEquals(get_option('convertkit_version'), CONVERTKIT_PLUGIN_VERSION);

        // Confirm the event was scheduled by the update() call.
        $this->assertEquals(
            wp_get_schedule('convertkit_resource_refresh_' . $this->resource->type),
            $this->resource->wp_cron_schedule
        );
    }

    /**
     * Test that the WordPress Cron event for this resource works when valid API credentials
     * are specified in the Plugin's settings.
     * 
     * @since 1.9.7.4
     */
    public function testCronEventWithValidAPICredentials()
    {
        // Delete Resources from options table.
        delete_option($this->resource->settings_name);
        delete_option($this->resource->settings_name . '_last_queried');

        // Run the action as WordPress' Cron would.
        do_action('convertkit_resource_refresh_' . $this->resource->type);

        // Confirm that Resources now exist in the option table.
        $result = get_option($this->resource->settings_name);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', reset($result));
        $this->assertArrayHasKey('title', reset($result));
    }

    /**
     * Test that the WordPress Cron event for this resource errors when invalid API credentials
     * are specified in the Plugin's settings.
     * 
     * @since 1.9.7.4
     */
    public function testCronEventWithInvalidAPICredentials()
    {
        // Define invalid API Credentials.
        update_option(
            $this->settings::SETTINGS_NAME, [
            'api_key'    => 'fakeApiKey',
            'api_secret' => 'fakeApiSecret',
            ]
        );

        // Delete Resources from options table.
        delete_option($this->resource->settings_name);
        delete_option($this->resource->settings_name . '_last_queried');

        // Run the action as WordPress' Cron would.
        do_action('convertkit_resource_refresh_' . $this->resource->type);

        // Confirm that no Resources exist in the option table.
        $result = get_option($this->resource->settings_name);
        $this->assertFalse($result);
    }

    /**
     * Test that the WordPress Cron event for this resource was destroyed when the Plugin
     * is deactivated.
     * 
     * @since 1.9.7.4
     */
    public function testCronEventDestroyedOnPluginDeactivation()
    {
        // Deactivate Plugin.
        deactivate_plugins('convertkit/wp-convertkit.php');

        // Confirm scheduled event does not exist.
        $this->assertFalse(wp_get_schedule('convertkit_resource_refresh_' . $this->resource->type));
    }
	
    /**
     * Test that the refresh() function performs as expected.
     * 
     * @since 1.9.7.4
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
     * @since 1.9.7.4
     */
    public function testExpiry()
    {
        // Define the expected expiry date based on the resource class' $cache_duration setting.
        $expectedExpiryDate = date('Y-m-d', time() + $this->resource->cache_duration);

        // Fetch the actual expiry date set when the resource class was initialized.
        $expiryDate = date('Y-m-d', $this->resource->last_queried + $this->resource->cache_duration);

        // Confirm both dates match.
        $this->assertEquals($expectedExpiryDate, $expiryDate);
    }

    /**
     * Test that the get() function performs as expected.
     * 
     * @since 1.9.7.4
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
     * Test that the get_paginated_subset() function performs as expected when requesting one item from the first page.
     * 
     * @since 1.9.7.6
     */
    public function testGetPaginatedSubsetFirstPage()
    {
        $this->testPagination(
            $this->resource->get_paginated_subset(1, 1), // Paginated array of resources and metadata.
            1, // Page.
            1, // Per Page.
            true, // Has a next page, as more results in the resultset exist.
            false // Does not have a previous page, as this is the first page in the resultset.
        );
    }

    /**
     * Test that the get_paginated_subset() function performs as expected when requesting one item from a page
     * that is not the first or last page.
     * 
     * @since 1.9.7.6
     */
    public function testGetPaginatedSubsetMiddlePage()
    {
        $this->testPagination(
            $this->resource->get_paginated_subset(2, 1), // Paginated array of resources and metadata.
            2, // Page.
            1, // Per Page.
            true, // Has a next page, as more results in the resultset exist.
            true // Has a previous page, as more results in the resultset exist.
        );
    }

    /**
     * Test that the get_paginated_subset() function performs as expected when requesting one item from the last page.
     * 
     * @since 1.9.7.6
     */
    public function testGetPaginatedSubsetLastPage()
    {
        // Query the API to establish how many resources exist.
        $result = $this->resource->get();
        $lastPage = count($result);

        $this->testPagination(
            $this->resource->get_paginated_subset($lastPage, 1), // Paginated array of resources and metadata.
            $lastPage, // Page.
            1, // Per Page.
            false, // Does not have a next page, as this is the last page in the resultset.
            true // Has a previous page, as more results in the resultset exist.
        );
    }

    /**
     * Test that the count() function returns the number of resources.
     * 
     * @since 1.9.7.6
     */
    public function testCount()
    {
        $result = $this->resource->get();
        $this->assertEquals($this->resource->count(), count($result));
    }

    /**
     * Shared tests for paginated resources, ensuring the response contains expected values for pagination,
     * next/previous links etc.
     * 
     * @since 1.9.7.6
     * 
     * @param array $result      Result.
     * @param int   $page        Page.
     * @param int   $perPage     Results per page.
     * @param bool  $hasNextPage Response should indicate pagination is available for older resources.
     * @param bool  $hasPrevPage Response should indicate pagination is available for newer resources.
     */
    private function testPagination($result, $page, $perPage, $hasNextPage, $hasPrevPage)
    {
        $this->assertNotInstanceOf(WP_Error::class, $result);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('items', $result);
        $this->assertArrayHasKey('page', $result);
        $this->assertArrayHasKey('per_page', $result);
        $this->assertArrayHasKey('has_next_page', $result);
        $this->assertArrayHasKey('has_prev_page', $result);

        // Check posts exist.
        $this->assertIsArray($result);
        $this->assertCount($perPage, $result['items']);
        $this->assertArrayHasKey('id', reset($result['items']));
        $this->assertArrayHasKey('title', reset($result['items']));

        // Check other array values are as expected.
        $this->assertEquals($result['page'], $page);
        $this->assertEquals($result['per_page'], $perPage);
        $this->assertEquals($result['has_next_page'], $hasNextPage);
        $this->assertEquals($result['has_prev_page'], $hasPrevPage);
    }

    /**
     * Test that the exist() function performs as expected.
     * 
     * @since 1.9.7.4
     */
    public function testExist()
    {
        // Confirm that the function returns true, because resources exist.
        $result = $this->resource->exist();
        $this->assertSame($result, true);
    }
}