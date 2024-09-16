<?php
/**
 * Tests for ConvertKit Forms when adding and editing Categories.
 *
 * @since   2.0.3
 */
class CategoryFormCest
{
	/**
	 * Run common actions before running the test functions in this class.
	 *
	 * @since   2.0.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _before(AcceptanceTester $I)
	{
		// Activate and Setup ConvertKit plugin.
		$I->activateConvertKitPlugin($I);
		$I->setupConvertKitPlugin($I);
		$I->setupConvertKitPluginResources($I);
	}

	/**
	 * Test that the expected Form is displayed when the user:
	 * - Creates a Category in WordPress, selecting the ConvertKit Form to display,
	 * - Creates a WordPress Post assigned to the created Category.
	 *
	 * @since   2.0.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddCategoryWithValidFormSetting(AcceptanceTester $I)
	{
		// Navigate to Posts > Categories.
		$I->amOnAdminPage('edit-tags.php?taxonomy=category');

		// Confirm that settings have label[for] attributes.
		$I->seeInSource('<label for="wp-convertkit-form">');

		// Create Category.
		$I->fillField('tag-name', 'ConvertKit: Create Category');
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Check the order of the Form resources are alphabetical, with the Default option prepending the Forms.
		$I->checkSelectFormOptionOrder(
			$I,
			'#wp-convertkit-form',
			[
				'Default',
			]
		);

		// Save.
		$I->click('Add New Category');

		// Confirm Category saved.
		$I->waitForElementVisible('.notice-success');

		// Get the Category ID from the table.
		$termID = (int) str_replace( 'tag-', '', $I->grabAttributeFrom('#the-list tr:first-child', 'id') );

		// Create Post, assigned to ConvertKit Category.
		$postID = $I->havePostInDatabase(
			[
				'post_type'  => 'post',
				'post_title' => 'ConvertKit: Inherit Form from Add Category',
				'tax_input'  => [
					[ 'category' => (int) $termID ],
				],
			]
		);

		// Load the Post on the frontend site.
		$I->amOnPage('/?p=' . $postID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeFormOutput($I, $_ENV['CONVERTKIT_API_FORM_ID'], false);
	}

	/**
	 * Test that the expected Form is displayed when the user:
	 * - Edits an existing Category in WordPress, selecting the ConvertKit Form to display,
	 * - Creates a WordPress Post assigned to the edited Category.
	 *
	 * @since   2.0.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testEditCategoryWithValidFormSetting(AcceptanceTester $I)
	{
		// Create Category.
		$termID = $I->haveTermInDatabase( 'ConvertKit: Edit Category', 'category' );
		$termID = $termID[0];

		// Create Post, assigned to ConvertKit Category.
		$postID = $I->havePostInDatabase(
			[
				'post_type'  => 'post',
				'post_title' => 'ConvertKit: Inherit Form from Edit Category',
				'tax_input'  => [
					[ 'category' => (int) $termID ],
				],
			]
		);

		// Edit the Term, defining a Form.
		$I->amOnAdminPage('term.php?taxonomy=category&tag_ID=' . $termID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that settings have label[for] attributes.
		$I->seeInSource('<label for="wp-convertkit-form">');

		// Check the order of the Form resources are alphabetical, with the Default option prepending the Forms.
		$I->checkSelectFormOptionOrder(
			$I,
			'#wp-convertkit-form',
			[
				'Default',
			]
		);

		// Change Form to value specified in the .env file.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);

		// Click Update.
		$I->click('Update');

		// Wait for the page to load.
		$I->waitForElementVisible('#wpfooter');

		// Check that the update succeeded.
		$I->seeElementInDOM('div.notice-success');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Load the Post on the frontend site.
		$I->amOnPage('/?p=' . $postID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeFormOutput($I, $_ENV['CONVERTKIT_API_FORM_ID'], false);
	}

	/**
	 * Test that the expected Form is displayed on the Category archive above the Posts
	 * when the user:
	 * - Creates a Category in WordPress, selecting the ConvertKit Form to display before the Posts
	 * - Creates a WordPress Post assigned to the created Category.
	 *
	 * @since   2.4.9.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddCategoryWithFormPositionBefore(AcceptanceTester $I)
	{
		// Navigate to Posts > Categories.
		$I->amOnAdminPage('edit-tags.php?taxonomy=category');

		// Create Category.
		$I->fillField('tag-name', 'ConvertKit: Position: Before');
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->selectOption('wp-convertkit[form_position]', 'before');

		// Save.
		$I->click('Add New Category');

		// Confirm Category saved.
		$I->waitForElementVisible('.notice-success');

		// Get the Category ID from the table.
		$termID = (int) str_replace( 'tag-', '', $I->grabAttributeFrom('#the-list tr:first-child', 'id') );

		// Create Post, assigned to ConvertKit Category.
		$postID = $I->havePostInDatabase(
			[
				'post_type'  => 'post',
				'post_title' => 'ConvertKit: Post for Position: Before',
				'tax_input'  => [
					[ 'category' => (int) $termID ],
				],
			]
		);

		// Load the Category archive on the frontend site.
		$I->amOnPage('/category/convertkit-position-before');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm form is after closing h1 element.
		$I->seeInSource(
			'</h1>

	<form action="https://app.convertkit.com/forms/' . $_ENV['CONVERTKIT_API_FORM_ID'] . '/subscriptions"'
		);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeFormOutput($I, $_ENV['CONVERTKIT_API_FORM_ID'], false);
	}

	/**
	 * Test that the expected Form is displayed on the Category archive above the Posts
	 * when the user:
	 * - Creates a Category in WordPress, selecting the ConvertKit Form to display after the Posts
	 * - Creates a WordPress Post assigned to the created Category.
	 *
	 * @since   2.4.9.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddCategoryWithFormPositionAfter(AcceptanceTester $I)
	{
		// Navigate to Posts > Categories.
		$I->amOnAdminPage('edit-tags.php?taxonomy=category');

		// Create Category.
		$I->fillField('tag-name', 'ConvertKit: Position: After');
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->selectOption('wp-convertkit[form_position]', 'after');

		// Save.
		$I->click('Add New Category');

		// Confirm Category saved.
		$I->waitForElementVisible('.notice-success');

		// Get the Category ID from the table.
		$termID = (int) str_replace( 'tag-', '', $I->grabAttributeFrom('#the-list tr:first-child', 'id') );

		// Create Post, assigned to ConvertKit Category.
		$postID = $I->havePostInDatabase(
			[
				'post_type'  => 'post',
				'post_title' => 'ConvertKit: Post for Position: After',
				'tax_input'  => [
					[ 'category' => (int) $termID ],
				],
			]
		);

		// Load the Category archive on the frontend site.
		$I->amOnPage('/category/convertkit-position-after');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm form is after closing div element.
		$I->seeInSource(
			'</div>
	<form action="https://app.convertkit.com/forms/' . $_ENV['CONVERTKIT_API_FORM_ID'] . '/subscriptions"'
		);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeFormOutput($I, $_ENV['CONVERTKIT_API_FORM_ID'], false);
	}

	/**
	 * Test that the expected Form is displayed on the Category archive above the Posts
	 * when the user:
	 * - Edits an existing Category in WordPress, selecting the ConvertKit Form to display before the Posts
	 * - Creates a WordPress Post assigned to the created Category.
	 *
	 * @since   2.4.9.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testEditCategoryWithFormPositionBefore(AcceptanceTester $I)
	{
		// Create Category.
		$termID = $I->haveTermInDatabase( 'ConvertKit: Edit: Position: Before', 'category' );
		$termID = $termID[0];

		// Create Post, assigned to ConvertKit Category.
		$postID = $I->havePostInDatabase(
			[
				'post_type'  => 'post',
				'post_title' => 'ConvertKit: Post: Edit: Position: Before',
				'tax_input'  => [
					[ 'category' => (int) $termID ],
				],
			]
		);

		// Edit the Term, defining a Form.
		$I->amOnAdminPage('term.php?taxonomy=category&tag_ID=' . $termID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that settings have label[for] attributes.
		$I->seeInSource('<label for="wp-convertkit-form">');

		// Check the order of the Form resources are alphabetical, with the Default option prepending the Forms.
		$I->checkSelectFormOptionOrder(
			$I,
			'#wp-convertkit-form',
			[
				'Default',
			]
		);

		// Change Form to value specified in the .env file.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->selectOption('wp-convertkit[form_position]', 'before');

		// Click Update.
		$I->click('Update');

		// Wait for the page to load.
		$I->waitForElementVisible('#wpfooter');

		// Check that the update succeeded.
		$I->seeElementInDOM('div.notice-success');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Load the Category archive on the frontend site.
		$I->amOnPage('/category/convert-kit-edit-position-before');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm form is after closing h1 element.
		$I->seeInSource(
			'</h1>

	<form action="https://app.convertkit.com/forms/' . $_ENV['CONVERTKIT_API_FORM_ID'] . '/subscriptions"'
		);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeFormOutput($I, $_ENV['CONVERTKIT_API_FORM_ID'], false);
	}

	/**
	 * Test that the expected Form is displayed on the Category archive above the Posts
	 * when the user:
	 * - Edits an existing Category in WordPress, selecting the ConvertKit Form to display after the Posts
	 * - Creates a WordPress Post assigned to the created Category.
	 *
	 * @since   2.4.9.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testEditCategoryWithFormPositionAfter(AcceptanceTester $I)
	{
		// Create Category.
		$termID = $I->haveTermInDatabase( 'ConvertKit: Edit: Position: After', 'category' );
		$termID = $termID[0];

		// Create Post, assigned to ConvertKit Category.
		$postID = $I->havePostInDatabase(
			[
				'post_type'  => 'post',
				'post_title' => 'ConvertKit: Post: Edit: Position: After',
				'tax_input'  => [
					[ 'category' => (int) $termID ],
				],
			]
		);

		// Edit the Term, defining a Form.
		$I->amOnAdminPage('term.php?taxonomy=category&tag_ID=' . $termID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that settings have label[for] attributes.
		$I->seeInSource('<label for="wp-convertkit-form">');

		// Check the order of the Form resources are alphabetical, with the Default option prepending the Forms.
		$I->checkSelectFormOptionOrder(
			$I,
			'#wp-convertkit-form',
			[
				'Default',
			]
		);

		// Change Form to value specified in the .env file.
		$I->fillSelect2Field($I, '#select2-wp-convertkit-form-container', $_ENV['CONVERTKIT_API_FORM_NAME']);
		$I->selectOption('wp-convertkit[form_position]', 'after');

		// Click Update.
		$I->click('Update');

		// Wait for the page to load.
		$I->waitForElementVisible('#wpfooter');

		// Check that the update succeeded.
		$I->seeElementInDOM('div.notice-success');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Load the Category archive on the frontend site.
		$I->amOnPage('/category/convert-kit-edit-position-after');

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm form is after closing div element.
		$I->seeInSource(
			'</div>
	<form action="https://app.convertkit.com/forms/' . $_ENV['CONVERTKIT_API_FORM_ID'] . '/subscriptions"'
		);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeFormOutput($I, $_ENV['CONVERTKIT_API_FORM_ID'], false);
	}

	/**
	 * Test that the Default Form specified at Plugin level for Posts displays when:
	 * - A Category was created with ConvertKit Form = 'None' when 1.9.5.2 or earlier of the Plugin was activated,
	 * - The WordPress Post is set to use the Default Form,
	 * - A Default Form is set in the Plugin settings.
	 *
	 * 1.9.5.2 and earlier stored the 'None' option as 'default' for Categories, meaning that the Post (or Plugin) default form
	 * should be used.
	 *
	 * 1.9.6.0 and later changed the value to 0 for Categories, bringing it in line with the Post Form's 'None'
	 * setting.
	 *
	 * @since   1.9.7.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testAddNewPostUsingDefaultFormWithCategoryCreatedBefore1960(AcceptanceTester $I)
	{
		// Create Category as if it were created / edited when the ConvertKit Plugin < 1.9.6.0
		// was active.
		$termID = $I->haveTermInDatabase(
			'ConvertKit 1.9.5.2 and earlier',
			'category',
			[
				'meta' => [
					'ck_default_form' => 'default', // Emulate how 1.9.5.2 and earlier store this setting.
				],
			]
		);
		$termID = $termID[0];

		// Create Post, assigned to Category.
		$postID = $I->havePostInDatabase(
			[
				'post_type'  => 'post',
				'post_title' => 'ConvertKit: Default Form: Category Created before 1.9.6.0',
				'tax_input'  => [
					[ 'category' => $termID ],
				],
			]
		);

		// Downgrade the Plugin version to simulate an upgrade.
		$I->haveOptionInDatabase('convertkit_version', '2.4.9');

		// Load admin screen.
		$I->amOnAdminPage('index.php');

		// Load the Post on the frontend site.
		$I->amOnPage('/?p=' . $postID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form.
		$I->seeFormOutput($I, $_ENV['CONVERTKIT_API_FORM_ID'], false);
	}

	/**
	 * Tests that existing Category settings stored in the Term Meta key [] are
	 * automatically migrated when updating the Plugin to 2.4.9.1 or higher.
	 *
	 * @since   2.4.9.1
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function testCategorySettingsMigratedOnUpgrade(AcceptanceTester $I)
	{
		// Create Category as if it were created / edited when the ConvertKit Plugin < 2.4.9.1
		// was active.
		$termID = $I->haveTermInDatabase(
			'ConvertKit 2.4.9.1 and earlier',
			'category',
			[
				'meta' => [
					'ck_default_form' => $_ENV['CONVERTKIT_API_FORM_ID'],
				],
			]
		);
		$termID = $termID[0];

		// Create Post, assigned to Category.
		$postID = $I->havePostInDatabase(
			[
				'post_type'  => 'post',
				'post_title' => 'ConvertKit: Default Form: Category Created before 1.9.6.0',
				'tax_input'  => [
					[ 'category' => $termID ],
				],
			]
		);

		// Downgrade the Plugin version to simulate an upgrade.
		$I->haveOptionInDatabase('convertkit_version', '2.4.9');

		// Load admin screen.
		$I->amOnAdminPage('index.php');

		// Check Category settings structure has been updated to the new meta key.
		$I->dontSeeTermMetaInDatabase(
			[
				'term_id'  => $termID,
				'meta_key' => 'ck_default_form',
			]
		);
		$I->seeTermMetaInDatabase(
			[
				'term_id'    => $termID,
				'meta_key'   => '_wp_convertkit_term_meta',
				'meta_value' => [
					'form'          => $_ENV['CONVERTKIT_API_FORM_ID'],
					'form_position' => '',
				],
			]
		);

		// Load the Post on the frontend site.
		$I->amOnPage('/?p=' . $postID);

		// Check that no PHP warnings or notices were output.
		$I->checkNoWarningsAndNoticesOnScreen($I);

		// Confirm that one ConvertKit Form is output in the DOM.
		// This confirms that there is only one script on the page for this form, which renders the form,
		// and that the Category settings were correctly mapped.
		$I->seeFormOutput($I, $_ENV['CONVERTKIT_API_FORM_ID'], false);
	}

	/**
	 * Deactivate and reset Plugin(s) after each test, if the test passes.
	 * We don't use _after, as this would provide a screenshot of the Plugin
	 * deactivation and not the true test error.
	 *
	 * @since   2.0.3
	 *
	 * @param   AcceptanceTester $I  Tester.
	 */
	public function _passed(AcceptanceTester $I)
	{
		$I->deactivateConvertKitPlugin($I);
		$I->resetConvertKitPlugin($I);
	}
}
