<?php

class DatabaseCest
{
    public function _before(FunctionalTester $I)
    {
    }

    // tests
    public function tryToTest(FunctionalTester $I)
    {
    	global $wpdb;

	    $url = get_site_url();
	    codecept_debug($url);

	    $prefix = $wpdb->get_blog_prefix();

	    codecept_debug($prefix);

    }
}
