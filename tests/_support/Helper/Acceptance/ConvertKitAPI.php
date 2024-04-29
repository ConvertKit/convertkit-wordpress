<?php
namespace Helper\Acceptance;

/**
 * Helper methods and actions related to the ConvertKit API,
 * which are then available using $I->{yourFunctionName}.
 *
 * @since   1.9.6
 */
class ConvertKitAPI extends \Codeception\Module
{
	/**
	 * Check the given email address exists as a subscriber.
	 *
	 * @param   AcceptanceTester $I             AcceptanceTester.
	 * @param   string           $emailAddress   Email Address.
	 */
	public function apiCheckSubscriberExists($I, $emailAddress)
	{
		// Run request.
		$results = $this->apiRequest(
			'subscribers',
			'GET',
			[
				'email_address' => $emailAddress,
				'include_total_count' => true,
			]
		);

		// Check at least one subscriber was returned and it matches the email address.
		$I->assertGreaterThan(0, $results['pagination']['total_count']);
		$I->assertEquals($emailAddress, $results['subscribers'][0]['email_address']);
	}

	/**
	 * Check the given subscriber ID has been assigned to the given tag ID.
	 *
	 * @param   AcceptanceTester $I             AcceptanceTester.
	 * @param   int              $subscriberID  Subscriber ID.
	 * @param   int              $tagID         Tag ID.
	 */
	public function apiCheckSubscriberHasTag($I, $subscriberID, $tagID)
	{
		// Run request.
		$results = $this->apiRequest(
			'subscribers/' . $subscriberID . '/tags',
			'GET'
		);

		// Confirm the tag has been assigned to the subscriber.
		$I->assertEquals($tagID, $results['tags'][0]['id']);
	}

	/**
	 * Check the given email address does not exists as a subscriber.
	 *
	 * @param   AcceptanceTester $I             AcceptanceTester.
	 * @param   string           $emailAddress   Email Address.
	 */
	public function apiCheckSubscriberDoesNotExist($I, $emailAddress)
	{
		// Run request.
		$results = $this->apiRequest(
			'subscribers',
			'GET',
			[
				'email_address' => $emailAddress,
			]
		);

		// Check no subscribers are returned by this request.
		$I->assertEquals(0, $results['total_subscribers']);
	}

	/**
	 * Subscribes the given email address. Useful for
	 * creating a subscriber to use in tests.
	 *
	 * @param   string $emailAddress   Email Address.
	 * @return  int                    Subscriber ID
	 */
	public function apiSubscribe($emailAddress)
	{
		// Run request.
		$result = $this->apiRequest(
			'subscribers',
			'POST',
			[
				'email_address' => $emailAddress,
			]
		);

		// Return subscriber ID.
		return $result['subscriber']['id'];
	}

	/**
	 * Unsubscribes the given email address. Useful for clearing the API
	 * between tests.
	 *
	 * @param   string $emailAddress   Email Address.
	 */
	public function apiUnsubscribe($emailAddress)
	{
		// Run request.
		$this->apiRequest(
			'unsubscribe',
			'PUT',
			[
				'email' => $emailAddress,
			]
		);
	}

	/**
	 * Fetches the given broadcast from ConvertKit.
	 *
	 * @since   2.4.0
	 *
	 * @param   int $broadcastID    Broadcast ID.
	 */
	public function apiGetBroadcast($broadcastID)
	{
		// Run request.
		return $this->apiRequest(
			'broadcasts/' . $broadcastID,
			'GET'
		);
	}

	/**
	 * Deletes the given broadcast from ConvertKit.
	 *
	 * @since   2.4.0
	 *
	 * @param   int $broadcastID    Broadcast ID.
	 */
	public function apiDeleteBroadcast($broadcastID)
	{
		// Run request.
		$this->apiRequest(
			'broadcasts/' . $broadcastID,
			'DELETE'
		);
	}

	/**
	 * Sends a request to the ConvertKit API, typically used to read an endpoint to confirm
	 * that data in an Acceptance Test was added/edited/deleted successfully.
	 *
	 * @param   string $endpoint   Endpoint.
	 * @param   string $method     Method (GET|POST|PUT).
	 * @param   array  $params     Endpoint Parameters.
	 */
	public function apiRequest($endpoint, $method = 'GET', $params = array())
	{
		// Send request.
		$client = new \GuzzleHttp\Client();
		switch ($method) {
			case 'GET':
				$result = $client->request(
					$method,
					'https://api.convertkit.com/v4/' . $endpoint . '?' . http_build_query($params),
					[
						'headers' => [
							'Authorization' => 'Bearer ' . $_ENV['CONVERTKIT_OAUTH_ACCESS_TOKEN'],
							'timeout'       => 5,
						],
					]
				);
				break;

			default:
				$result = $client->request(
					$method,
					'https://api.convertkit.com/v4/' . $endpoint,
					[
						'headers' => [
							'Accept'        => 'application/json',
							'Content-Type'  => 'application/json; charset=utf-8',
							'Authorization' => 'Bearer ' . $_ENV['CONVERTKIT_OAUTH_ACCESS_TOKEN'],
							'timeout'       => 5,
						],
						'body'    => (string) json_encode($params), // phpcs:ignore WordPress.WP.AlternativeFunctions
					]
				);
				break;
		}

		// Return JSON decoded response.
		return json_decode($result->getBody()->getContents(), true);
	}
}
