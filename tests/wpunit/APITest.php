<?php

class APITest extends \Codeception\TestCase\WPTestCase
{
	/**
	 * @var \WpunitTester
	 */
	protected $tester;

	/**
	 * Holds the ConvertKit API class.
	 * 
	 * @since 	1.9.6.9
	 * 
	 * @var 	ConvertKit_API
	 */
	private $api;

	/**
	 * Holds the expected WP_Error code.
	 * 
	 * @since 	1.9.6.9
	 * 
	 * @var 	string
	 */
	private $errorCode = 'convertkit_api_error';
	
	/**
	 * Performs actions before each test.
	 * 
	 * @since 	1.9.6.9
	 */
	public function setUp(): void
	{
		parent::setUp();
		$this->api = new ConvertKit_API( $_ENV['CONVERTKIT_API_KEY'], $_ENV['CONVERTKIT_API_SECRET'] );
		sleep(2);
	}

	/**
	 * Performs actions after each test.
	 * 
	 * @since 	1.9.6.9
	 */
	public function tearDown(): void
	{
		unset($this->api);
		parent::tearDown();
	}

	/**
	 * Test that supplying invalid API credentials to the API class returns a WP_Error.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testNoAPICredentials()
	{
		$api = new ConvertKit_API();
		$result = $api->account();
		$this->assertInstanceOf(WP_Error::class, $result);
		$this->assertEquals($result->get_error_code(), $this->errorCode);
	}

	/**
	 * Test that supplying valid API credentials to the API class returns the expected account information.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testAccount()
	{
		$result = $this->api->account();
		$this->assertNotInstanceOf(WP_Error::class, $result);
		$this->assertIsArray($result);
		$this->assertArrayHasKey('name', $result);
		$this->assertArrayHasKey('plan_type', $result);
		$this->assertArrayHasKey('primary_email_address', $result);
		$this->assertEquals($_ENV['CONVERTKIT_API_SUBSCRIBER_EMAIL'], $result['primary_email_address']);
	}

	/**
	 * Test that the `get_subscription_forms()` function returns expected data.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testGetSubscriptionForms()
	{
		$result = $this->api->get_subscription_forms();
		$this->assertNotInstanceOf(WP_Error::class, $result);
		$this->assertIsArray($result);
		$this->assertArrayHasKey('id', reset($result));
		$this->assertArrayHasKey('form_id', reset($result));
	}

	/**
	 * Test that the `get_forms()` function returns expected data.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testGetForms()
	{
		$result = $this->api->get_forms();
		$this->assertNotInstanceOf(WP_Error::class, $result);
		$this->assertIsArray($result);
		$this->assertArrayHasKey('id', reset($result));
		$this->assertArrayHasKey('name', reset($result));
		$this->assertArrayHasKey('format', reset($result));
		$this->assertArrayHasKey('embed_js', reset($result));
	}

	/**
	 * Test that the `form_subscribe()` function returns expected data
	 * when valid parameters are provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testFormSubscribe()
	{
		$result = $this->api->form_subscribe( $_ENV['CONVERTKIT_API_FORM_ID'], $_ENV['CONVERTKIT_API_SUBSCRIBER_EMAIL'], 'First');
		$this->assertNotInstanceOf(WP_Error::class, $result);
		$this->assertIsArray($result);
		$this->assertArrayHasKey('subscription', $result);
	}

	/**
	 * Test that the `form_subscribe()` function returns a WP_Error
	 * when an empty $form_id parameter is provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testFormSubscribeWithEmptyFormID()
	{
		$result = $this->api->form_subscribe( '', $_ENV['CONVERTKIT_API_SUBSCRIBER_EMAIL'], 'First');
		$this->assertInstanceOf(WP_Error::class, $result);
		$this->assertEquals($result->get_error_code(), $this->errorCode);
		$this->assertEquals('form_subscribe(): the form_id parameter is empty.', $result->get_error_message());
	}

	/**
	 * Test that the `form_subscribe()` function returns a WP_Error
	 * when an empty $email parameter is provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testFormSubscribeWithEmptyEmail()
	{
		$result = $this->api->form_subscribe( $_ENV['CONVERTKIT_API_FORM_ID'], '', 'First');
		$this->assertInstanceOf(WP_Error::class, $result);
		$this->assertEquals($result->get_error_code(), $this->errorCode);
		$this->assertEquals('form_subscribe(): the email parameter is empty.', $result->get_error_message());
	}

	/**
	 * Test that the `get_landing_pages()` function returns expected data.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testGetLandingPages()
	{
		$result = $this->api->get_landing_pages();
		$this->assertNotInstanceOf(WP_Error::class, $result);
		$this->assertIsArray($result);
		$this->assertArrayHasKey('id', reset($result));
		$this->assertArrayHasKey('name', reset($result));
		$this->assertArrayHasKey('format', reset($result));
		$this->assertArrayHasKey('embed_js', reset($result));
		$this->assertArrayHasKey('embed_url', reset($result));
	}

	/**
	 * Test that the `get_sequences()` function returns expected data.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testGetSequences()
	{
		$result = $this->api->get_sequences();
		$this->assertNotInstanceOf(WP_Error::class, $result);
		$this->assertIsArray($result);
		$this->assertArrayHasKey('id', reset($result));
		$this->assertArrayHasKey('name', reset($result));
	} 

	/**
	 * Test that the `sequence_subscribe()` function returns expected data
	 * when valid parameters are provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testSequenceSubscribe()
	{
		$result = $this->api->sequence_subscribe( $_ENV['CONVERTKIT_API_SEQUENCE_ID'], $_ENV['CONVERTKIT_API_SUBSCRIBER_EMAIL']);
		$this->assertNotInstanceOf(WP_Error::class, $result);
		$this->assertIsArray($result);
		$this->assertArrayHasKey('subscription', $result);
	}

	/**
	 * Test that the `sequence_subscribe()` function returns a WP_Error
	 * when an empty $sequence_id parameter is provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testSequenceSubscribeWithEmptySequenceID()
	{
		$result = $this->api->sequence_subscribe( '', $_ENV['CONVERTKIT_API_SUBSCRIBER_EMAIL']);
		$this->assertInstanceOf(WP_Error::class, $result);
		$this->assertEquals($result->get_error_code(), $this->errorCode);
		$this->assertEquals('sequence_subscribe(): the sequence_id parameter is empty.', $result->get_error_message());
	}

	/**
	 * Test that the `sequence_subscribe()` function returns a WP_Error
	 * when an empty $email parameter is provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testSequenceSubscribeWithEmptyEmail()
	{
		$result = $this->api->sequence_subscribe( $_ENV['CONVERTKIT_API_SEQUENCE_ID'], '');
		$this->assertInstanceOf(WP_Error::class, $result);
		$this->assertEquals($result->get_error_code(), $this->errorCode);
		$this->assertEquals('sequence_subscribe(): the email parameter is empty.', $result->get_error_message());
	}

	/**
	 * Test that the `get_tags()` function returns expected data.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testGetTags()
	{
		$result = $this->api->get_tags();
		$this->assertNotInstanceOf(WP_Error::class, $result);
		$this->assertIsArray($result);
		$this->assertArrayHasKey('id', reset($result));
		$this->assertArrayHasKey('name', reset($result));
	} 

	/**
	 * Test that the `tag_subscribe()` function returns expected data
	 * when valid parameters are provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testTagSubscribe()
	{
		$result = $this->api->tag_subscribe( $_ENV['CONVERTKIT_API_TAG_ID'], $_ENV['CONVERTKIT_API_SUBSCRIBER_EMAIL']);
		$this->assertNotInstanceOf(WP_Error::class, $result);
		$this->assertIsArray($result);
		$this->assertArrayHasKey('subscription', $result);
	}

	/**
	 * Test that the `tag_subscribe()` function returns a WP_Error
	 * when an empty $tag_id parameter is provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testTagSubscribeWithEmptyTagID()
	{
		$result = $this->api->tag_subscribe( '', $_ENV['CONVERTKIT_API_SUBSCRIBER_EMAIL']);
		$this->assertInstanceOf(WP_Error::class, $result);
		$this->assertEquals($result->get_error_code(), $this->errorCode);
		$this->assertEquals('tag_subscribe(): the tag_id parameter is empty.', $result->get_error_message());
	}

	/**
	 * Test that the `tag_subscribe()` function returns a WP_Error
	 * when an empty $email parameter is provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testTagSubscribeWithEmptyEmail()
	{
		$result = $this->api->tag_subscribe( $_ENV['CONVERTKIT_API_TAG_ID'], '');
		$this->assertInstanceOf(WP_Error::class, $result);
		$this->assertEquals($result->get_error_code(), $this->errorCode);
		$this->assertEquals('tag_subscribe(): the email parameter is empty.', $result->get_error_message());
	} 

	/**
	 * Test that the `get_subscriber_by_email()` function returns expected data
	 * when valid parameters are provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testGetSubscriberByEmail()
	{
		$result = $this->api->get_subscriber_by_email($_ENV['CONVERTKIT_API_SUBSCRIBER_EMAIL']);
		$this->assertNotInstanceOf(WP_Error::class, $result);
		$this->assertIsArray($result);
		$this->assertArrayHasKey('id', $result);
		$this->assertEquals($result['id'], $_ENV['CONVERTKIT_API_SUBSCRIBER_ID']);
	} 

	/**
	 * Test that the `get_subscriber_by_email()` function returns a WP_Error
	 * when an empty $email parameter is provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testGetSubscriberByEmailWithEmptyEmail()
	{
		$result = $this->api->get_subscriber_by_email('');
		$this->assertInstanceOf(WP_Error::class, $result);
		$this->assertEquals($result->get_error_code(), $this->errorCode);
		$this->assertEquals('get_subscriber_by_email(): the email parameter is empty.', $result->get_error_message());
	} 

	/**
	 * Test that the `get_subscriber_by_id()` function returns expected data
	 * when valid parameters are provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testGetSubscriberByID()
	{
		$result = $this->api->get_subscriber_by_id($_ENV['CONVERTKIT_API_SUBSCRIBER_ID']);
		$this->assertNotInstanceOf(WP_Error::class, $result);
		$this->assertIsArray($result);
		$this->assertArrayHasKey('id', $result);
		$this->assertEquals($result['id'], $_ENV['CONVERTKIT_API_SUBSCRIBER_ID']);
	} 

	/**
	 * Test that the `get_subscriber_by_id()` function returns a WP_Error
	 * when an empty $email parameter is provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testGetSubscriberByIDWithEmptyID()
	{
		$result = $this->api->get_subscriber_by_id('');
		$this->assertInstanceOf(WP_Error::class, $result);
		$this->assertEquals($result->get_error_code(), $this->errorCode);
		$this->assertEquals('get_subscriber_by_id(): the subscriber_id parameter is empty.', $result->get_error_message());
	} 

	/**
	 * Test that the `get_subscriber_tags()` function returns expected data
	 * when valid parameters are provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testGetSubscriberTags()
	{
		$result = $this->api->get_subscriber_tags($_ENV['CONVERTKIT_API_SUBSCRIBER_ID']);
		$this->assertNotInstanceOf(WP_Error::class, $result);
		$this->assertIsArray($result);
		$this->assertCount(1, $result);
		$this->assertArrayHasKey('id', $result[0]);
		$this->assertArrayHasKey('name', $result[0]);
		$this->assertEquals($result[0]['id'], $_ENV['CONVERTKIT_API_TAG_ID']);
	} 

	/**
	 * Test that the `get_subscriber_by_id()` function returns a WP_Error
	 * when an empty $email parameter is provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testGetSubscriberTagsWithEmptyID()
	{
		$result = $this->api->get_subscriber_tags('');
		$this->assertInstanceOf(WP_Error::class, $result);
		$this->assertEquals($result->get_error_code(), $this->errorCode);
		$this->assertEquals('get_subscriber_tags(): the subscriber_id parameter is empty.', $result->get_error_message());
	} 

	/**
	 * Test that the `get_subscriber_id()` function returns expected data
	 * when valid parameters are provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testGetSubscriberID()
	{
		$result = $this->api->get_subscriber_id($_ENV['CONVERTKIT_API_SUBSCRIBER_EMAIL']);
		$this->assertNotInstanceOf(WP_Error::class, $result);
		$this->assertEquals($_ENV['CONVERTKIT_API_SUBSCRIBER_ID'], $result);
	} 

	/**
	 * Test that the `get_subscriber_id()` function returns a WP_Error
	 * when an empty $email parameter is provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testGetSubscriberIDWithEmptyEmail()
	{
		$result = $this->api->get_subscriber_id('');
		$this->assertInstanceOf(WP_Error::class, $result);
		$this->assertEquals($result->get_error_code(), $this->errorCode);

		// get_subscriber_by_email() is deliberate in this error message, as get_subscriber_id() calls get_subscriber_by_email().
		$this->assertEquals('get_subscriber_by_email(): the email parameter is empty.', $result->get_error_message());
	} 

	/**
	 * Test that the `unsubscribe()` function returns expected data
	 * when valid parameters are provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testUnsubscribe()
	{
		$result = $this->api->unsubscribe($_ENV['CONVERTKIT_API_SUBSCRIBER_EMAIL']);
		$this->assertNotInstanceOf(WP_Error::class, $result);
	} 

	/**
	 * Test that the `unsubscribe()` function returns a WP_Error
	 * when an empty $email parameter is provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testUnsubscribeWithEmptyEmail()
	{
		$result = $this->api->unsubscribe('');
		$this->assertInstanceOf(WP_Error::class, $result);
		$this->assertEquals($result->get_error_code(), $this->errorCode);
		$this->assertEquals('unsubscribe(): the email parameter is empty.', $result->get_error_message());
	}

	/**
	 * Test that the `get_subscriber()` function is backward compatible.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testBackwardCompatGetSubscriber()
	{
		$result = $this->api->get_subscriber($_ENV['CONVERTKIT_API_SUBSCRIBER_ID']);
		$this->assertNotInstanceOf(WP_Error::class, $result);
	} 

	/**
	 * Test that the `add_tag()` function is backward compatible.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testBackwardCompatAddTag()
	{
		$result = $this->api->add_tag($_ENV['CONVERTKIT_API_TAG_ID'], [
			'email' => $_ENV['CONVERTKIT_API_SUBSCRIBER_EMAIL'],
		]);
		$this->assertNotInstanceOf(WP_Error::class, $result);
		$this->assertIsArray($result);
		$this->assertArrayHasKey('subscription', $result);
	} 

	/**
	 * Test that the `add_tag()` function is backward compatible and returns a WP_Error
	 * when an empty $tag_id parameter is provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testBackwardCompatAddTagWithEmptyTagID()
	{
		$result = $this->api->add_tag( '', [
			'email' => $_ENV['CONVERTKIT_API_SUBSCRIBER_EMAIL']
		]);
		$this->assertInstanceOf(WP_Error::class, $result);
		$this->assertEquals($result->get_error_code(), $this->errorCode);
		$this->assertEquals('tag_subscribe(): the tag_id parameter is empty.', $result->get_error_message());
	}

	/**
	 * Test that the `add_tag()` function is backward compatible and returns a WP_Error
	 * when an empty $email parameter is provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testBackwardCompatAddTagWithEmptyEmail()
	{
		$result = $this->api->add_tag( $_ENV['CONVERTKIT_API_TAG_ID'], [
			'email' => '',
		]);
		$this->assertInstanceOf(WP_Error::class, $result);
		$this->assertEquals($result->get_error_code(), $this->errorCode);
		$this->assertEquals('tag_subscribe(): the email parameter is empty.', $result->get_error_message());
	}  

	/**
	 * Test that the `unsubscribe()` function is backward compatible.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testBackwardCompatFormUnsubscribe()
	{
		$result = $this->api->form_unsubscribe([
			'email' => $_ENV['CONVERTKIT_API_SUBSCRIBER_EMAIL']
		]);
		$this->assertNotInstanceOf(WP_Error::class, $result);
	} 

	/**
	 * Test that the `unsubscribe()` function is backward compatible and returns a WP_Error
	 * when an empty $email parameter is provided.
	 * 
	 * @since 	1.9.6.9
	 */
	public function testBackwardCompatFormUnsubscribeWithEmptyEmail()
	{
		$result = $this->api->form_unsubscribe([
			'email' => '',
		]);
		$this->assertInstanceOf(WP_Error::class, $result);
		$this->assertEquals($result->get_error_code(), $this->errorCode);
		$this->assertEquals('unsubscribe(): the email parameter is empty.', $result->get_error_message());
	}
}