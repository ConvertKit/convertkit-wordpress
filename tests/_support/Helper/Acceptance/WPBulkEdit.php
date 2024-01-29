<?php
namespace Helper\Acceptance;

/**
 * Helper methods and actions related to WordPress' Bulk Edit functionality,
 * which are then available using $I->{yourFunctionName}.
 *
 * @since   1.9.6
 */
class WPBulkEdit extends \Codeception\Module
{
	/**
	 * Bulk Edits the given Post IDs, changing form field values and saving.
	 *
	 * @since   1.9.8.0
	 *
	 * @param   AcceptanceHelper $I              Acceptance Helper.
	 * @param   string           $postType       Programmatic Post Type.
	 * @param   array            $postIDs        Post IDs.
	 * @param   array            $configuration  Configuration (field => value key/value array).
	 */
	public function bulkEdit($I, $postType, $postIDs, $configuration)
	{
		// Open Bulk Edit form for the Posts.
		$I->openBulkEdit($I, $postType, $postIDs);

		// Apply configuration.
		foreach ($configuration as $field => $attributes) {
			// Field ID will be prefixed with wp-convertkit-bulk-edit-.
			$fieldID = 'wp-convertkit-bulk-edit-' . $field;

			// Check that the field exists.
			$I->seeElementInDOM('#convertkit-bulk-edit #' . $fieldID);

			// Depending on the field's type, define its value.
			switch ($attributes[0]) {
				case 'select':
					$I->selectOption('#convertkit-bulk-edit #' . $fieldID, $attributes[1]);
					break;
				default:
					$I->fillField('#convertkit-bulk-edit #' . $fieldID, $attributes[1]);
					break;
			}
		}

		// Scroll so that the Update button is in the viewport.
		$I->scrollTo('#bulk-edit .inline-edit-wrapper:last-child');

		// Click Update.
		$I->click('#bulk_edit');

		// Wait for the WP_List_Table of Pages to load.
		$I->waitForElementVisible('tbody#the-list');

		// Confirm that Bulk Editing saved with no errors.
		switch ($postType) {
			case 'post':
			case 'page':
				$I->see(count($postIDs) . ' ' . $postType . 's updated.');
				break;
			default:
				$I->see(count($postIDs) . ' posts updated.');
				break;
		}
	}

	/**
	 * Opens the Bulk Edit form for the given Post ID.
	 *
	 * @since   1.9.8.1
	 *
	 * @param   AcceptanceHelper $I              Acceptance Helper.
	 * @param   string           $postType       Programmatic Post Type.
	 * @param   array            $postIDs        Post IDs.
	 */
	public function openBulkEdit($I, $postType, $postIDs)
	{
		// Navigate to Post Type's WP_List_Table.
		$I->amOnAdminPage('edit.php?post_type=' . $postType);

		// Check boxes for Post IDs.
		foreach ($postIDs as $postID) {
			$I->checkOption('#cb-select-' . $postID);
		}

		// Select Edit from the Bulk actions dropdown.
		$I->selectOption('#bulk-action-selector-top', 'Edit');

		// Click Apply button.
		$I->click('#doaction');
	}
}
