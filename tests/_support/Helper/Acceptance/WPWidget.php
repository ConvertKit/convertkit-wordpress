<?php
namespace Helper\Acceptance;

// Define any custom actions related to metaboxes that
// would be used across multiple tests.
// These are then available in $I->{yourFunctionName}

class WPWidget extends \Codeception\Module
{
	/**
	 * Configure a given legacy widget's fields with the given values.
	 * 
	 * @since 	1.9.7.6
	 * 
	 * @param 	AcceptanceTester 	$I 						Acceptance Tester.
	 * @param 	string 				$blockName 				Block Name (e.g. 'ConvertKit Form').
	 * @param 	string 				$blockProgrammaticName 	Programmatic Block Name (e.g. 'convertkit-form').
	 * @param 	bool|array 			$blockConfiguration 	Block Configuration (field => value key/value array).
	 */
	public function addLegacyWidget($I, $blockName, $blockProgrammaticName, $blockConfiguration = false)
	{
		// Navigate to Appearance > Widgets.
		$I->amOnAdminPage('widgets.php');

		// Dismiss welcome message.
		$I->maybeCloseGutenbergWelcomeModal($I);

		// Click Add Block Button.
		$I->click('button.edit-widgets-header-toolbar__inserter-toggle');

		// When the Blocks sidebar appears, search for the legacy widget.
		$I->waitForElementVisible('.interface-interface-skeleton__secondary-sidebar');
		$I->fillField('.block-editor-inserter__content input[type=search]', $blockName);

		// First matching item will be the legacy widget; any blocks will follow.
		// We can't target using the CSS selector button.editor-block-list-item-legacy-widget/{name}, as Codeception
		// fails stating this is malformed CSS.
		$I->seeElementInDOM('.block-editor-inserter__panel-content .block-editor-block-types-list__list-item button[tabindex="0"]');
		$I->click('.block-editor-inserter__panel-content .block-editor-block-types-list__list-item button[tabindex="0"]');
		
		// If a Block configuration is specified, apply it to the Block now.
		if ($blockConfiguration) {
			$I->waitForElementVisible('.wp-block-legacy-widget form');

			foreach ($blockConfiguration as $field=>$attributes) {
				$fieldID = '#widget-' . str_replace('-', '_', $blockProgrammaticName) . '-1-' . $field;
				
				// Depending on the field's type, define its value.
				switch ($attributes[0]) {
					case 'select':
						$I->selectOption($fieldID, $attributes[1]);
						break;
					default:
						$I->fillField($fieldID, $attributes[1]);
						break;
				}
			}
		}

		// Wait for Update button to change its state from disabled.
		$I->wait(2);

		// Save.
		$I->click('Update');

		// Wait for save to complete.
		$I->wait(2);
	}

	/**
	 * Check a given legacy widget is displayed on the frontend site.
	 * 
	 * @since 	1.9.7.6
	 * 
	 * @param 	AcceptanceTester 	$I 						Acceptance Tester.
	 * @param 	string 				$blockName 				Block Name (e.g. 'ConvertKit Form').
	 * @param 	string 				$blockProgrammaticName 	Programmatic Block Name (e.g. 'convertkit-form').
	 * @param 	bool|array 			$blockConfiguration 	Block Configuration (field => value key/value array).
	 */
	public function seeLegacyWidget($I, $blockProgrammaticName, $expectedMarkup)
	{
		// View the home page.
		$I->amOnPage('/');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body');

		// Confirm that the widget exists in an expected widget area.
		$I->seeElementInDOM('aside.widget-area .widget_'.str_replace('-', '_', $blockProgrammaticName));

		// Confirm that the ConvertKit Form is displayed in the widget.
		$I->seeElementInDOM($expectedMarkup);
	}

	/**
	 * Add the given block when editing widgets using Gutenberg.
	 * 
	 * If a block configuration is specified, applies it to the newly added block.
	 * 
	 * @since 	1.9.7.6
	 * 
	 * @param 	AcceptanceTester 	$I 						Acceptance Tester.
	 * @param 	string 				$blockName 				Block Name (e.g. 'ConvertKit Form').
	 * @param 	string 				$blockProgrammaticName 	Programmatic Block Name (e.g. 'convertkit-form').
	 * @param 	bool|array 			$blockConfiguration 	Block Configuration (field => value key/value array).
	 */
	public function addBlockWidget($I, $blockName, $blockProgrammaticName, $blockConfiguration = false)
	{
		// Navigate to Appearance > Widgets.
		$I->amOnAdminPage('widgets.php');

		// Dismiss welcome message.
		$I->maybeCloseGutenbergWelcomeModal($I);

		// Click Add Block Button.
		$I->click('button.edit-widgets-header-toolbar__inserter-toggle');

		// When the Blocks sidebar appears, search for the legacy widget.
		$I->waitForElementVisible('.interface-interface-skeleton__secondary-sidebar');
		$I->fillField('.block-editor-inserter__content input[type=search]', $blockName);
		$I->seeElementInDOM('.block-editor-inserter__panel-content button.editor-block-list-item-' . $blockProgrammaticName);
		$I->click('.block-editor-inserter__panel-content button.editor-block-list-item-' . $blockProgrammaticName);

		// If a Block configuration is specified, apply it to the Block now.
		if ($blockConfiguration) {
			$I->waitForElementVisible('.interface-interface-skeleton__sidebar[aria-label="Widgets settings"]');

			foreach ($blockConfiguration as $field=>$attributes) {
				// Field ID will be block's programmatic name with underscores instead of hyphens,
				// followed by the attribute name.
				$fieldID = '#' . str_replace('-', '_', $blockProgrammaticName) . '_' . $field;
				
				// Depending on the field's type, define its value.
				switch ($attributes[0]) {
					case 'select':
						$I->selectOption($fieldID, $attributes[1]);
						break;
					case 'toggle':
						$I->click($field);
						break;
					default:
						$I->fillField($fieldID, $attributes[1]);
						break;
				}
			}
		}

		// Save.
		$I->click('Update');

		// Confirm that saving worked.
		$I->waitForElementVisible('.components-snackbar__content');
	}

	/**
	 * Check a given block widget is displayed on the frontend site.
	 * 
	 * @since 	1.9.7.6
	 * 
	 * @param 	AcceptanceTester 	$I 						Acceptance Tester.
	 * @param 	string 				$blockName 				Block Name (e.g. 'ConvertKit Form').
	 * @param 	string 				$blockProgrammaticName 	Programmatic Block Name (e.g. 'convertkit-form').
	 * @param 	bool|array 			$blockConfiguration 	Block Configuration (field => value key/value array).
	 */
	public function seeBlockWidget($I, $blockProgrammaticName, $expectedMarkup)
	{
		// View the home page.
		$I->amOnPage('/');

		// Confirm that the ConvertKit Form is displayed in the widget area.
		$I->seeElementInDOM($expectedMarkup);
	}

	/**
	 * Removes all widgets from widget areas, resetting their state to blank
	 * for the next test.
	 * 
	 * @since 	1.9.7.6
	 * 
	 * @param 	AcceptanceTester 	$I 						Acceptance Tester.
	 */
	public function resetWidgets($I)
	{
		$I->dontHaveOptionInDatabase('sidebar_widgets');
		$I->dontHaveOptionInDatabase('widget_block');

		// List any ConvertKit blocks here, so they're also removed as widgets from sidebars/footers.
		$I->dontHaveOptionInDatabase('widget_convertkit_form');
		$I->dontHaveOptionInDatabase('widget_convertkit_broadcasts');
	}
}
