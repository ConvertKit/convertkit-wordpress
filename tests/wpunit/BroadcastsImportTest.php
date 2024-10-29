<?php
/**
 * Tests for the ConvertKit_Broadcasts_Importer functions.
 *
 * @since   2.6.4
 */
class BroadcastsImportTest extends \Codeception\TestCase\WPTestCase
{
	/**
	 * The testing implementation.
	 *
	 * @var \WpunitTester.
	 */
	protected $tester;

	/**
	 * Performs actions before each test.
	 *
	 * @since   2.6.4
	 */
	public function setUp(): void
	{
		parent::setUp();

		// Activate Plugin.
		activate_plugins('convertkit/wp-convertkit.php');

		// Initialize the class we want to test.
		$this->importer = new ConvertKit_Broadcasts_Importer();

		// Confirm initialization didn't result in an error.
		$this->assertNotInstanceOf(WP_Error::class, $this->importer);
	}

	/**
	 * Performs actions after each test.
	 *
	 * @since   2.4.2
	 */
	public function tearDown(): void
	{
		// Destroy the class we tested.
		unset($this->importer);

		// Deactivate Plugin.
		deactivate_plugins('convertkit/wp-convertkit.php');

		parent::tearDown();
	}

	/**
	 * Test that the import_broadcast() method works.
	 *
	 * @since   2.6.4
	 */
	public function testImportBroadcast()
	{
		// Import broadcast.
		$result = $this->importer->import_broadcast($_ENV['CONVERTKIT_API_BROADCAST_ID']);
		
		// @TODO Inspect created Post to run assertions.
		var_dump($result);
		die();

	}

	/**
	 * Test that the import_broadcast() method works when
	 * a Post Status is defined.
	 *
	 * @since   2.6.4
	 */
	public function testImportBroadcastWithPostStatus()
	{

	}

	/**
	 * Test that the import_broadcast() method works when
	 * a WordPress User ID is defined as the author.
	 *
	 * @since   2.6.4
	 */
	public function testImportBroadcastWithAuthorID()
	{

	}

	/**
	 * Test that the import_broadcast() method works when
	 * a Category ID is defined.
	 *
	 * @since   2.6.4
	 */
	public function testImportBroadcastWithCategoryID()
	{

	}

	/**
	 * Test that the import_broadcast() method works when
	 * importing the Broadcast's thumbnail as the Post's
	 * Featured Image is enabled.
	 *
	 * @since   2.6.4
	 */
	public function testImportBroadcastWithImportThumbnailEnabled()
	{

	}

	/**
	 * Test that the import_broadcast() method works when
	 * importing images to the Media Library is enabled.
	 *
	 * @since   2.6.4
	 */
	public function testImportBroadcastWithImportImagesEnabled()
	{

	}

	/**
	 * Test that the import_broadcast() method works when
	 * disabling styles is enabled.
	 *
	 * @since   2.6.4
	 */
	public function testImportBroadcastWithDisableStylesEnabled()
	{

	}
}
