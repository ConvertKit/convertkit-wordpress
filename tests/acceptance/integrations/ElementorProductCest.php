<?php
/**
 * Tests for the ConvertKit Product Elementor Widget.
 *
 * @since 2.0.0
 */
class ElementorProductCest {

	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since 2.0.0
	 *
	 * @param AcceptanceTester $I Tester
	 */
	public function _before( AcceptanceTester $I ) {
		$I->activateConvertKitPlugin( $I );
		$I->activateThirdPartyPlugin( $I, 'elementor' );
		$I->setupConvertKitPlugin( $I );
		$I->enableDebugLog( $I );
	}

	/**
	 * Test the Product widget is registered in Elementor.
	 *
	 * @since 2.0.0
	 *
	 * @param AcceptanceTester $I Tester
	 */
	public function testProductWidgetIsRegistered( AcceptanceTester $I ) {
		// Add a Page using the Gutenberg editor.
		$I->addGutenbergPage( $I, 'page', 'ConvertKit: Page: Product: Elementor: Registered' );

		// Click Edit with Elementor button.
		$I->click( '#elementor-switch-mode-button' );

		// When Elementor loads, search for the ConvertKit Product block.
		$I->waitForElementVisible( '#elementor-panel-elements-search-input' );
		$I->fillField( '#elementor-panel-elements-search-input', 'ConvertKit Product' );

		// Confirm that the Product widget is displayed as an option.
		$I->seeElementInDOM( '#elementor-panel-elements .elementor-element' );
	}

	/**
	 * Test the Product block works when using valid parameters.
	 *
	 * @since 2.0.0
	 *
	 * @param AcceptanceTester $I Tester
	 */
	public function testProductWidgetWithValidParameters( AcceptanceTester $I ) {
		// Create Page with Product widget in Elementor.
		$pageID = $this->_createPageWithProductWidget(
			$I,
			'ConvertKit: Page: Product: Elementor Widget: Valid Params',
			array(
				'product' => $_ENV['CONVERTKIT_API_PRODUCT_ID'],
				'text'    => 'Buy my product',
			)
		);

		// Load Page.
		$I->amOnPage( '?p=' . $pageID );

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen( $I );

		// Confirm that the block displays.
		$I->seeProductOutput( $I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy my product' );
	}

	/**
	 * Test the Product block's hex colors work when defined.
	 *
	 * @since 2.0.0
	 *
	 * @param AcceptanceTester $I Tester
	 */
	public function testProductWidgetWithHexColorParameters( AcceptanceTester $I ) {
		// Define colors.
		$backgroundColor = '#ee1616';
		$textColor       = '#1212c0';

		// Create Page with Product widget in Elementor.
		$pageID = $this->_createPageWithProductWidget(
			$I,
			'ConvertKit: Page: Product: Elementor Widget: Hex Colors',
			array(
				'product'          => $_ENV['CONVERTKIT_API_PRODUCT_ID'],
				'text'             => 'Buy my product',
				'background_color' => $backgroundColor,
				'text_color'       => $textColor,
			)
		);

		// Load Page.
		$I->amOnPage( '?p=' . $pageID );

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen( $I );

		// Confirm that the block displays.
		$I->seeProductOutput( $I, $_ENV['CONVERTKIT_API_PRODUCT_URL'], 'Buy my product', $textColor, $backgroundColor );
	}

	/**
	 * Create a Page in the database comprising of Elementor Page Builder data
	 * containing a ConvertKit Product widget.
	 *
	 * Codeception's dragAndDrop() method doesn't support dropping an element into an iframe, which is
	 * how Elementor works for adding widgets to a Page.
	 *
	 * Therefore, we directly create a Page in the database, with Elementor's data structure
	 * as if we added the Product widget to a Page edited in Elementor.
	 *
	 * testProductWidgetIsRegistered() above is a sanity check that the Product Widget is registered
	 * and available to users in Elementor.
	 *
	 * @since 2.0.0
	 *
	 * @param  AcceptanceTester $I        Tester.
	 * @param  string           $title    Page Title.
	 * @param  array            $settings Widget settings.
	 * @return int                             Page ID
	 */
	private function _createPageWithProductWidget( AcceptanceTester $I, $title, $settings ) {
		return $I->havePostInDatabase(
			array(
				'post_title'  => $title,
				'post_type'   => 'page',
				'post_status' => 'publish',
				'meta_input'  => array(
					// Elementor.
					'_elementor_data'          => array(
						0 => array(
							'id'       => '39bb59d',
							'elType'   => 'section',
							'settings' => array(),
							'elements' => array(
								array(
									'id'       => 'b7e0e57',
									'elType'   => 'column',
									'settings' => array(
										'_column_size' => 100,
										'_inline_size' => null,
									),
									'elements' => array(
										array(
											'id'         => 'a73a905',
											'elType'     => 'widget',
											'settings'   => $settings,
											'widgetType' => 'convertkit-elementor-product',
										),
									),
								),
							),
						),
					),
					'_elementor_version'       => '3.6.1',
					'_elementor_edit_mode'     => 'builder',
					'_elementor_template_type' => 'wp-page',

					// Configure ConvertKit Plugin to not display a default Form,
					// as we are testing for the Form in Elementor.
					'_wp_convertkit_post_meta' => array(
						'form'         => '-1',
						'landing_page' => '',
						'tag'          => '',
					),
				),
			)
		);
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since 2.0.0
	 *
	 * @param AcceptanceTester $I Tester
	 */
	public function _passed( AcceptanceTester $I ) {
		$I->deactivateThirdPartyPlugin( $I, 'elementor' );
		$I->deactivateConvertKitPlugin( $I );
		$I->resetConvertKitPlugin( $I );
	}
}
