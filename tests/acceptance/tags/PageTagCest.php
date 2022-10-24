<?php
/**
 * Tests for ConvertKit Tags on WordPress Pages.
 *
 * @since 1.9.6
 */
class PageTagCest {

	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since 1.9.6
	 *
	 * @param AcceptanceTester $I Tester
	 */
	public function _before( AcceptanceTester $I ) {
		// Activate and Setup ConvertKit plugin
		$I->activateConvertKitPlugin( $I );
		$I->setupConvertKitPlugin( $I );
		$I->enableDebugLog( $I );
	}

	/**
	 * Test that 'None' Tag specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 *
	 * @since 1.9.6
	 *
	 * @param AcceptanceTester $I Tester
	 */
	public function testAddNewPageUsingNoTag( AcceptanceTester $I ) {
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage( $I, 'page', 'ConvertKit: Page: Tag: None' );

		// Configure metabox's Tag setting = None.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			array(
				'tag' => array( 'select2', 'None' ),
			)
		);

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage( $I );

		// Confirm that the tag parameter is not set to the Tag ID.
		$I->dontSeeInSource( '"tag":"' . $_ENV['CONVERTKIT_API_TAG_ID'] . '"' );
	}

	/**
	 * Test that the Tag specified in the Page Settings works when
	 * creating and viewing a new WordPress Page.
	 *
	 * @since 1.9.6
	 *
	 * @param AcceptanceTester $I Tester
	 */
	public function testAddNewPageUsingDefinedTag( AcceptanceTester $I ) {
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage( $I, 'page', 'ConvertKit: Page: Tag: ' . $_ENV['CONVERTKIT_API_TAG_NAME'] );

		// Configure metabox's Tag setting to the value specified in the .env file.
		$I->configureMetaboxSettings(
			$I,
			'wp-convertkit-meta-box',
			array(
				'tag' => array( 'select2', $_ENV['CONVERTKIT_API_TAG_NAME'] ),
			)
		);

		// Get Tag ID.
		$tagID = $I->grabValueFrom( '#wp-convertkit-tag' );

		// Confirm it matches the Tag ID in the .env file.
		$I->assertEquals( $tagID, $_ENV['CONVERTKIT_API_TAG_ID'] );

		// Publish and view the Page on the frontend site.
		$I->publishAndViewGutenbergPage( $I );

		// Confirm that the post_has_tag parameter is set to true in the source code.
		$I->seeInSource( '"tag":"' . $tagID . '"' );
	}

	/**
	 * Test that the defined tag is honored when chosen via
	 * WordPress' Quick Edit functionality.
	 *
	 * @since 1.9.8.0
	 *
	 * @param AcceptanceTester $I Tester
	 */
	public function testQuickEditUsingDefinedTag( AcceptanceTester $I ) {
		// Programmatically create a Page.
		$pageID = $I->havePostInDatabase(
			array(
				'post_type'  => 'page',
				'post_title' => 'ConvertKit: Page: Tag: ' . $_ENV['CONVERTKIT_API_TAG_NAME'] . ': Quick Edit',
			)
		);

		// Quick Edit the Page in the Pages WP_List_Table.
		$I->quickEdit(
			$I,
			'page',
			$pageID,
			array(
				'tag' => array( 'select', $_ENV['CONVERTKIT_API_TAG_NAME'] ),
			)
		);

		// Load the Page on the frontend site.
		$I->amOnPage( '/?p=' . $pageID );

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen( $I );

		// Confirm that the post_has_tag parameter is set to true in the source code.
		$I->seeInSource( '"tag":"' . $_ENV['CONVERTKIT_API_TAG_ID'] . '"' );
	}

	/**
	 * Test that the defined tag displays when chosen via
	 * WordPress' Bulk Edit functionality.
	 *
	 * @since 1.9.8.0
	 *
	 * @param AcceptanceTester $I Tester
	 */
	public function testBulkEditUsingDefinedTag( AcceptanceTester $I ) {
		// Programmatically create two Pages.
		$pageIDs = array(
			$I->havePostInDatabase(
				array(
					'post_type'  => 'page',
					'post_title' => 'ConvertKit: Page: Tag: ' . $_ENV['CONVERTKIT_API_TAG_NAME'] . ': Bulk Edit #1',
				)
			),
			$I->havePostInDatabase(
				array(
					'post_type'  => 'page',
					'post_title' => 'ConvertKit: Page: Tag: ' . $_ENV['CONVERTKIT_API_TAG_NAME'] . ': Bulk Edit #2',
				)
			),
		);

		// Bulk Edit the Pages in the Pages WP_List_Table.
		$I->bulkEdit(
			$I,
			'page',
			$pageIDs,
			array(
				'tag' => array( 'select', $_ENV['CONVERTKIT_API_TAG_NAME'] ),
			)
		);

		// Iterate through Pages to run frontend tests.
		foreach ( $pageIDs as $pageID ) {
			// Load the Page on the frontend site.
			$I->amOnPage( '/?p=' . $pageID );

			// Check that no PHP warnings or notices were output.
			$I->checkNoWarningsAndNoticesOnScreen( $I );

			// Confirm that the post_has_tag parameter is set to true in the source code.
			$I->seeInSource( '"tag":"' . $_ENV['CONVERTKIT_API_TAG_ID'] . '"' );
		}
	}

	/**
	 * Test that the existing settings are honored and not changed
	 * when the Bulk Edit options are set to 'No Change'.
	 *
	 * @since 1.9.8.0
	 *
	 * @param AcceptanceTester $I Tester
	 */
	public function testBulkEditWithNoChanges( AcceptanceTester $I ) {
		// Programmatically create two Pages with a defined tag.
		$pageIDs = array(
			$I->havePostInDatabase(
				array(
					'post_type'  => 'page',
					'post_title' => 'ConvertKit: Page: Tag: ' . $_ENV['CONVERTKIT_API_TAG_NAME'] . ': Bulk Edit with No Change #1',
					'meta_input' => array(
						'_wp_convertkit_post_meta' => array(
							'form'         => '',
							'landing_page' => '',
							'tag'          => $_ENV['CONVERTKIT_API_TAG_ID'],
						),
					),
				)
			),
			$I->havePostInDatabase(
				array(
					'post_type'  => 'page',
					'post_title' => 'ConvertKit: Page: Form: ' . $_ENV['CONVERTKIT_API_FORM_NAME'] . ': Bulk Edit with No Change #2',
					'meta_input' => array(
						'_wp_convertkit_post_meta' => array(
							'form'         => '',
							'landing_page' => '',
							'tag'          => $_ENV['CONVERTKIT_API_TAG_ID'],
						),
					),
				)
			),
		);

		// Bulk Edit the Pages in the Pages WP_List_Table.
		$I->bulkEdit(
			$I,
			'page',
			$pageIDs,
			array(
				'tag' => array( 'select', '— No Change —' ),
			)
		);

		// Iterate through Pages to run frontend tests.
		foreach ( $pageIDs as $pageID ) {
			// Load the Page on the frontend site.
			$I->amOnPage( '/?p=' . $pageID );

			// Check that no PHP warnings or notices were output.
			$I->checkNoWarningsAndNoticesOnScreen( $I );

			// Confirm that the post_has_tag parameter is set to true in the source code.
			$I->seeInSource( '"tag":"' . $_ENV['CONVERTKIT_API_TAG_ID'] . '"' );
		}
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since 1.9.6.7
	 *
	 * @param AcceptanceTester $I Tester
	 */
	public function _passed( AcceptanceTester $I ) {
		$I->deactivateConvertKitPlugin( $I );
		$I->resetConvertKitPlugin( $I );
	}
}
