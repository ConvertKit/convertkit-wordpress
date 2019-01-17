<?php

class CK_Tests_Filters extends WP_UnitTestCase {
	function setUp() {
		parent::setUp();
	}

	/**
	 * Test that content filters get added
	 */
	function test_the_content() {
		global $wp_filter;
		$this->assertarrayHasKey( 'WP_ConvertKit::append_form', $wp_filter['the_content'][10] );
	}

	function test_mce_external_plugins() {
		global $wp_filter;
//		$this->assertarrayHasKey( 'ConvertKit_TinyMCE::add_tinymce_plugin', $wp_filter['mce_external_plugins'][10] );
		$this->assertarrayHasKey( 'ConvertKit_TinyMCE::register_mce_button', $wp_filter['mce_buttons'][10] );
	}
}
