<?php

class ActivePluginNoticesAndWarningsCest
{
    public function _before(AcceptanceTester $I)
    {
    }

	/**
	 * @param AcceptanceTester $I
	 * @param \Codeception\Scenario $scenario
	 */
	public function testWarningAndNoticeIsNotShown(AcceptanceTester $I, \Codeception\Scenario $scenario)
	{
		$I->wantTo( 'Test that activating the plugin without entering API credentials and visiting a post will not result in a PHP warning or notice.' );

		/*
		 * Turn on error reporting and display, so even if wp-config.php somehow has them turned off, we'll
		 * still see them if the test fails
		 */
		error_reporting( E_ALL );
		ini_set( 'display_errors', 1 );

		$settings = array(
			'default_form' => array()
		);
		update_option( '_wp_convertkit_settings', $settings );

		$category = uniqid();

		$category_id = $I->factory()->term->create( [
			                                         'name'     => $category,
			                                         'taxonomy' => 'category',
			                                         'slug'     => $category
		                                         ] );
		$post = $I->factory()->post->create( [
			                                     'post_category' => array( $category_id )
		                                     ] );

		$slug = get_post_field( 'post_name', $post );

		$I->amOnPage($slug);

		$I->cantSeeInSource( 'Warning' );
		$I->cantSeeInSource( 'Notice' );
	}

}
