<?php
namespace Helper\Acceptance;

// Define any custom actions related to the Classic Editor that
// would be used across multiple tests.
// These are then available in $I->{yourFunctionName}

class WPClassicEditor extends \Codeception\Module
{
	/**
	 * Add a Page, Post or Custom Post Type using the Classic Editor in WordPress.
	 * 
	 * @since 	1.9.7.5
	 * 
	 * @param 	AcceptanceTester 	$I 						Acceptance Tester.
	 */
	public function addClassicEditorPage($I, $postType = 'page', $title)
	{
		// Activate Classic Editor Plugin
		$I->activateThirdPartyPlugin($I, 'classic-editor');

		// Navigate to Post Type (e.g. Pages / Posts) > Add New
		$I->amOnAdminPage('post-new.php?post_type='.$postType);

		// Define the Title.
		$I->fillField('#title', $title);
	}

	/**
	 * Add the given shortcode when adding or editing a Page, Post or Custom Post Type
	 * in the Visual Editor (TinyMCE).
	 * 
	 * If a shortcode configuration is specified, applies it to the newly added shortcode.
	 * 
	 * @since 	1.9.7.5
	 * 
	 * @param 	AcceptanceTester 	$I 							Acceptance Tester.
	 * @param 	string 				$shortcodeName 				Shortcode Name (e.g. 'ConvertKit Form').
	 * @param 	string 				$shortcodeProgrammaticName 	Programmatic Shortcode Name (e.g. 'convertkit-form').
	 * @param 	bool|array 			$shortcodeConfiguration 	Shortcode Configuration (field => value key/value array).
	 * @param 	bool|string 		$expectedShortcodeOutput 	Expected Shortcode Output (e.g. [convertkit_form form="12345"]).
	 * @param 	string 				$targetEditor				Target TinyMCE editor instance.
	 */
	public function addVisualEditorShortcode($I, $shortcodeName, $shortcodeProgrammaticName, $shortcodeConfiguration = false, $expectedShortcodeOutput = false, $targetEditor = 'content')
	{
		// Click the Visual tab.
		$I->scrollTo('input#title');
		$I->click('button#'.$targetEditor.'-tmce');

		// Click the TinyMCE Button for this shortcode.
		$I->click('#wp-'.$targetEditor.'-editor-container div.mce-container div[aria-label="'.$shortcodeName.'"] button');

		// Wait for the modal's contents to load.
		$I->waitForElementVisible('#convertkit-modal-body input.button-primary');

		// If a shortcode configuration is specified, apply it to the shortcode's modal window now.
		if ($shortcodeConfiguration) {
			foreach ($shortcodeConfiguration as $field=>$attributes) {
				// Field ID will be the attribute name, prefixed with tinymce_modal
				$fieldID = '#tinymce_modal_' . $field;

				// Depending on the field's type, define its value.
				switch ($attributes[0]) {
					case 'select':
						$I->selectOption('#convertkit-modal-body-body '.$fieldID, $attributes[1]);
						break;
					case 'toggle':
						$I->selectOption('#convertkit-modal-body-body '.$fieldID, $attributes[1]);
						break;
					default:
						$I->fillField('#convertkit-modal-body-body '.$fieldID, $attributes[1]);
						break;
				}
			}
		}

		// Click the Insert button.
		$I->click('#convertkit-modal-body input.button-primary');

		// If the expected shortcode output is provided, check it exists in the Visual editor.
		if ($expectedShortcodeOutput) {
			$I->switchToIFrame('iframe#'.$targetEditor.'_ifr');
			$I->seeInSource($expectedShortcodeOutput);
			$I->switchToIFrame();
		}
	}

	/**
	 * Add the given shortcode when adding or editing a Page, Post or Custom Post Type
	 * in the Text Editor.
	 * 
	 * If a shortcode configuration is specified, applies it to the newly added shortcode.
	 * 
	 * @since 	1.9.7.5
	 * 
	 * @param 	AcceptanceTester 	$I 							Acceptance Tester.
	 * @param 	string 				$shortcodeName 				Shortcode Name (e.g. 'ConvertKit Form').
	 * @param 	string 				$shortcodeProgrammaticName 	Programmatic Shortcode Name (e.g. 'convertkit-form').
	 * @param 	bool|array 			$shortcodeConfiguration 	Shortcode Configuration (field => value key/value array).
	 * @param 	bool|string 		$expectedShortcodeOutput 	Expected Shortcode Output (e.g. [convertkit_form form="12345"]).
	 * @param 	string 				$targetEditor				ID of text editor instance.
	 */
	public function addTextEditorShortcode($I, $shortcodeName, $shortcodeProgrammaticName, $shortcodeConfiguration = false, $expectedShortcodeOutput = false, $targetEditor = 'content')
	{
		// Click the Text tab.
		$I->scrollTo('input#title');
		$I->click('button#'.$targetEditor.'-html');

		// Click the QuickTags Button for this shortcode.
		$I->click('input#qt_'.$targetEditor.'_'.$shortcodeProgrammaticName);

		// Wait for the modal's contents to load.
		$I->waitForElementVisible('#convertkit-quicktags-modal input.button-primary');

		// If a shortcode configuration is specified, apply it to the shortcode's modal window now.
		if ($shortcodeConfiguration) {
			foreach ($shortcodeConfiguration as $field=>$attributes) {
				// Field ID will be the attribute name, prefixed with tinymce_modal
				$fieldID = '#tinymce_modal_' . $field;
				
				// Depending on the field's type, define its value.
				switch ($attributes[0]) {
					case 'select':
						$I->selectOption($fieldID, $attributes[1]);
						break;
					case 'toggle':
						$I->selectOption($fieldID, $attributes[1]);
						break;
					default:
						$I->fillField($fieldID, $attributes[1]);
						break;
				}
			}
		}

		// Click the Insert button.
		$I->click('#convertkit-quicktags-modal input.button-primary');

		// If the expected shortcode output is provided, check it exists in the Text editor.
		if ($expectedShortcodeOutput) {
			$I->seeInField('textarea#'.$targetEditor, $expectedShortcodeOutput);
		}
	}

	/**
	 * Publish a Page, Post or Custom Post Type initiated by the addClassicEditorPage() function,
	 * loading it on the frontend web site.
	 * 
	 * @since 	1.9.7.5
	 * 
	 * @param 	AcceptanceTester 	$I 						Acceptance Tester.
	 */
	public function publishAndViewClassicEditorPage($I)
	{
		// Scroll to Publish meta box, so its buttons are not hidden.
		$I->scrollTo('#submitdiv');
		
		// Click the Publish button.
		$I->click('input#publish');

		// Wait for notice to display.
		$I->waitForElementVisible('.notice-success');
		
		// Load the Page on the frontend site.
		$I->click('.notice-success a');

		// Wait for frontend web site to load.
		$I->waitForElementVisible('body');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);
	}
}
