<?php
namespace Helper\Acceptance;

/**
 * Helper methods and actions related to WordPress' Gutenberg / Block editor,
 * which are then available using $I->{yourFunctionName}.
 *
 * @since   1.9.6
 */
class WPGutenberg extends \Codeception\Module
{
	/**
	 * Helper method to close the Gutenberg "Welcome to the block editor" dialog, which
	 * might show for each Page/Post test performed due to there being no persistence
	 * remembering that the user dismissed the dialog.
	 *
	 * @since   1.9.6
	 *
	 * @param   AcceptanceTester $I Acceptance Tester.
	 */
	public function maybeCloseGutenbergWelcomeModal($I)
	{
		try {
			$I->performOn(
				'.components-modal__screen-overlay',
				[
					'click' => '.components-modal__screen-overlay .components-modal__header button.components-button',
				],
				3
			);
		} catch ( \Facebook\WebDriver\Exception\TimeoutException $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			// No modal exists, so nothing to dismiss.
		}
	}

	/**
	 * Add a Page, Post or Custom Post Type using Gutenberg in WordPress.
	 *
	 * @since   1.9.7.5
	 *
	 * @param   AcceptanceTester $I          Acceptance Tester.
	 * @param   string           $postType   Post Type.
	 * @param   string           $title      Post Title.
	 */
	public function addGutenbergPage($I, $postType = 'page', $title = 'Gutenberg Title')
	{
		// Navigate to Post Type (e.g. Pages / Posts) > Add New.
		$I->amOnAdminPage('post-new.php?post_type=' . $postType);

		// Close the Gutenberg "Welcome to the block editor" dialog if it's displayed.
		$I->maybeCloseGutenbergWelcomeModal($I);

		// Define the Title.
		$I->fillField('.editor-post-title__input', $title);
	}

	/**
	 * Add the given block when adding or editing a Page, Post or Custom Post Type
	 * in Gutenberg.
	 *
	 * If a block configuration is specified, applies it to the newly added block.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I                      Acceptance Tester.
	 * @param   string           $blockName              Block Name (e.g. 'ConvertKit Form').
	 * @param   string           $blockProgrammaticName  Programmatic Block Name (e.g. 'convertkit-form').
	 * @param   bool|array       $blockConfiguration     Block Configuration (field => value key/value array).
	 */
	public function addGutenbergBlock($I, $blockName, $blockProgrammaticName, $blockConfiguration = false)
	{
		// Click Add Block Button.
		$I->click('button.edit-post-header-toolbar__inserter-toggle');

		// When the Blocks sidebar appears, search for the block.
		$I->waitForElementVisible('.interface-interface-skeleton__secondary-sidebar[aria-label="Block Library"]');
		$I->fillField('.block-editor-inserter__menu input[type=search]', $blockName);
		$I->seeElementInDOM('.block-editor-inserter__panel-content button.editor-block-list-item-' . $blockProgrammaticName);
		$I->click('.block-editor-inserter__panel-content button.editor-block-list-item-' . $blockProgrammaticName);

		// If a Block configuration is specified, apply it to the Block now.
		if ($blockConfiguration) {
			$I->waitForElementVisible('.interface-interface-skeleton__sidebar[aria-label="Editor settings"]');
			foreach ($blockConfiguration as $field => $attributes) {
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
	}

	/**
	 * Adds a paragraph block when adding or editing a Page, Post or Custom Post Type
	 * in Gutenberg.
	 *
	 * @since   2.0.0
	 *
	 * @param   AcceptanceTester $I      Acceptance Tester.
	 * @param   string           $text   Paragraph Text.
	 */
	public function addGutenbergParagraphBlock($I, $text)
	{
		$I->click('.is-root-container');
		$I->fillField('.is-root-container p', $text);
	}

	/**
	 * Adds a link to the given Page, Post or Custom Post Type Name in the last paragraph in the Gutenberg
	 * editor.
	 *
	 * @since   2.0.0
	 *
	 * @param   AcceptanceTester $I      Acceptance Tester.
	 * @param   string           $name   Page, Post or Custom Post Type Title/Name to link to.
	 */
	public function addGutenbergLinkToParagraph($I, $name)
	{
		// Focus away from paragraph and then back to the paragraph, so that the block toolbar displays.
		$I->click('div.edit-post-visual-editor__post-title-wrapper h1');
		$I->click('.is-root-container p');
		$I->waitForElementVisible('.is-root-container p.is-selected');

		// Insert link via block toolbar.
		$this->insertGutenbergLink($I, $name);

		// Confirm that the Product text exists in the paragraph.
		$I->see($name, '.is-root-container p.is-selected');
	}

	/**
	 * Adds a link to the given Page, Post or Custom Post Type Name in the selected Button block
	 * in the Gutenberg editor.
	 *
	 * @since   2.0.0
	 *
	 * @param   AcceptanceTester $I      Acceptance Tester.
	 * @param   string           $name   Page, Post or Custom Post Type Title/Name to link to.
	 */
	public function addGutenbergLinkToButton($I, $name)
	{
		// Enter text.
		$I->fillField('.is-root-container .wp-block-button .block-editor-rich-text__editable', $name);

		// Focus away from button and then back to the button, so that the block toolbar displays.
		$I->click('div.edit-post-visual-editor__post-title-wrapper h1');
		$I->click('.is-root-container .wp-block-button');
		$I->waitForElementVisible('.is-root-container div.is-selected');

		// Insert link via block toolbar.
		$this->insertGutenbergLink($I, $name);

		// Confirm that the Product text exists in the button.
		$I->see($name, '.is-root-container .wp-block-button');
	}

	/**
	 * Helper method to insert a link into the selected element, by:
	 * - clicking the link button in the selected block's toolbar,
	 * - searching for the Page, Post or Custom Post Type,
	 * - clicking the matched result to insert the link and text.
	 *
	 * The block must be selected in Gutenberg prior to calling this function.
	 *
	 * @since   2.0.0
	 *
	 * @param   AcceptanceTester $I      Acceptance Tester.
	 * @param   string           $name   Page, Post or Custom Post Type Title/Name to link to.
	 */
	private function insertGutenbergLink($I, $name)
	{
		// Click link button in block toolbar.
		$I->waitForElementVisible('.block-editor-block-toolbar button[aria-label="Link"]');
		$I->click('.block-editor-block-toolbar button[aria-label="Link"]');

		// Enter Product name in search field.
		$I->waitForElementVisible('.block-editor-link-control__search-input-wrapper input.block-editor-url-input__input');
		$I->fillField('.block-editor-link-control__search-input-wrapper input.block-editor-url-input__input', $name);
		$I->waitForElementVisible('.block-editor-link-control__search-results-wrapper');
		$I->see($name);

		// Click the Product name to create a link to it.
		$I->click($name, '.block-editor-link-control__search-results');
	}

	/**
	 * Check that the given block did not output any errors when rendered in the
	 * Gutenberg editor.
	 *
	 * @since   1.9.7.4
	 *
	 * @param   AcceptanceTester $I                      Acceptance Tester.
	 * @param   string           $blockName              Block Name (e.g. 'ConvertKit Form').
	 */
	public function checkGutenbergBlockHasNoErrors($I, $blockName)
	{
		// Wait a couple of seconds for the block to render.
		sleep(2);

		// Check that the "This block has encountered an error and cannot be previewed." element doesn't exist.
		$I->dontSeeElementInDOM('.block-editor-block-list__layout div[data-title="' . $blockName . '"] .block-editor-block-list__block-crash-warning');
	}

	/**
	 * Publish a Page, Post or Custom Post Type initiated by the addGutenbergPage() function,
	 * loading it on the frontend web site.
	 *
	 * @since   1.9.7.5
	 *
	 * @param   AcceptanceTester $I                      Acceptance Tester.
	 */
	public function publishAndViewGutenbergPage($I)
	{
		// Click the Publish button.
		$I->click('.editor-post-publish-button__button');

		// When the pre-publish panel displays, click Publish again.
		$I->performOn(
			'.editor-post-publish-panel__prepublish',
			function($I) {
				$I->click('.editor-post-publish-panel__header-publish-button .editor-post-publish-button__button');
			}
		);

		// Wait for confirmation that the Page published.
		$I->waitForElementVisible('.post-publish-panel__postpublish-buttons a.components-button');

		// Load the Page on the frontend site.
		$I->click('.post-publish-panel__postpublish-buttons a.components-button');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}
}
