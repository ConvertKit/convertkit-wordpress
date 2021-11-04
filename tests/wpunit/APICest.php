<?php
/**
 * Tests for the ConvertKit API class.
 * 
 * @since   1.0.0
 */
class APICest
{
    /**
     * Holds the ConvertKit API class.
     * 
     * @since   1.0.0
     *
     * @var     ConvertKit_API
     */
    private $api;

    /**
     * Run common actions before running the test functions in this class.
     * 
     * @since   1.0.0
     * 
     * @param   UnitTester    $I  Tester
     */
    public function _before(UnitTester $I)
    {
        $this->api = new ConvertKit_API( $_ENV['CONVERTKIT_API_KEY'], $_ENV['CONVERTKIT_API_SECRET'], false );
    }

    /**
     * Tests the add_tag() API class function.
     *
     * @since   1.0.0
     * 
     * @param   UnitTester    $I  Tester
     */
    public function testAddTag(UnitTester $I)
    {

    }

    /**
     * Tests the update_resources() API class function.
     *
     * @since   1.0.0
     * 
     * @param   UnitTester    $I  Tester
     */
    public function testUpdateResources(UnitTester $I)
    {
        
    }

    /**
     * Tests the update_account_name() API class function.
     *
     * @since   1.0.0
     * 
     * @param   UnitTester    $I  Tester
     */
    public function testUpdateAccountName(UnitTester $I)
    {
        
    }

    /**
     * Tests the maybe_update_option() API class function.
     *
     * @since   1.0.0
     * 
     * @param   UnitTester    $I  Tester
     */
    public function testMaybeUpdateOption(UnitTester $I)
    {
        
    }

    /**
     * Tests the get_resources() API class function.
     *
     * @since   1.0.0
     * 
     * @param   UnitTester    $I  Tester
     */
    public function testGetResources(UnitTester $I)
    {
        
    }

    /**
     * Tests the form_subscribe() API class function.
     *
     * @since   1.0.0
     * 
     * @param   UnitTester    $I  Tester
     */
    public function testFormSubscribe(UnitTester $I)
    {
        
    }

    /**
     * Tests the form_unsubscribe() API class function.
     *
     * @since   1.0.0
     * 
     * @param   UnitTester    $I  Tester
     */
    public function testFormUnsubscribe(UnitTester $I)
    {
        
    }

    /**
     * Tests the get_subscriber_id() API class function.
     *
     * @since   1.0.0
     * 
     * @param   UnitTester    $I  Tester
     */
    public function testGetSubscriberID(UnitTester $I)
    {
        
    }

    /**
     * Tests the get_subscriber() API class function.
     *
     * @since   1.0.0
     * 
     * @param   UnitTester    $I  Tester
     */
    public function testGetSubscriber(UnitTester $I)
    {
        
    }

    /**
     * Tests the get_subscriber_tags() API class function.
     *
     * @since   1.0.0
     * 
     * @param   UnitTester    $I  Tester
     */
    public function testGetSubscriberTags(UnitTester $I)
    {
        
    }

    /**
     * Tests the get_resource() API class function.
     *
     * @since   1.0.0
     * 
     * @param   UnitTester    $I  Tester
     */
    public function testGetResource(UnitTester $I)
    {
        
    }
}
