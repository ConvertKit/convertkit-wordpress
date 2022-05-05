<?php
namespace Helper\Acceptance;

// Define any custom actions related to Gutenberg that
// would be used across multiple tests.
// These are then available in $I->{yourFunctionName}

class WPGutenberg extends \Codeception\Module
{
	/**
	 * Helper method to close the Gutenberg "Welcome to the block editor" dialog, which
	 * might show for each Page/Post test performed due to there being no persistence
	 * remembering that the user dismissed the dialog.
	 * 
	 * @since 	1.9.6
	 */
	public function maybeCloseGutenbergWelcomeModal($I)
	{
		try {
			$I->performOn('.components-modal__screen-overlay', [
				'click' => '.components-modal__screen-overlay .components-modal__header button.components-button'
			], 3);
		} catch ( \Facebook\WebDriver\Exception\TimeoutException $e ) {
		}
	}

	/**
	 * Add the given block when adding or editing a Page, Post or Custom Post Type
	 * in Gutenberg.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 						Acceptance Tester.
	 * @param 	string 				$blockName 				Block Name (e.g. 'ConvertKit Form')
	 * @param 	string 				$blockProgrammaticName 	Programmatic Block Name (e.g. 'convertkit-form')
	 */
	public function addGutenbergBlock($I, $blockName, $blockProgrammaticName)
	{
		// Click Add Block Button.
		$I->click('button.edit-post-header-toolbar__inserter-toggle');

		// When the Blocks sidebar appears, search for the block.
		$I->waitForElementVisible('.interface-interface-skeleton__secondary-sidebar[aria-label="Block library"]');
		$I->fillField('.block-editor-inserter__content input[type=search]', $blockName);
		$I->seeElementInDOM('.block-editor-inserter__panel-content button.editor-block-list-item-' . $blockProgrammaticName);
		$I->click('.block-editor-inserter__panel-content button.editor-block-list-item-' . $blockProgrammaticName);
	}

	/**
	 * Check that the given block did not output any errors when rendered in the
	 * Gutenberg editor.
	 * 
	 * @since 	1.9.7.4
	 * 
	 * @param 	AcceptanceTester 	$I 						Acceptance Tester.
	 * @param 	string 				$blockName 				Block Name (e.g. 'ConvertKit Form')
	 */
	public function checkGutenbergBlockHasNoErrors($I, $blockName)
	{
		// Wait a couple of seconds for the block to render.
		sleep(2);

		// Check that the "This block has encountered an error and cannot be previewed." element doesn't exist.
		$I->dontSeeElementInDOM('.block-editor-block-list__layout div[data-title="'.$blockName.'"] .block-editor-block-list__block-crash-warning');
	}
}
