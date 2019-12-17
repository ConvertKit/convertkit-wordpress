<?php

class WooCommerceProductFormCest
{
    public function _before(AcceptanceTester $I)
    {
	    $I->loginAsAdmin();
    }

    public function testDefaultFormForWooCommerceProducts(AcceptanceTester $I)
    {
	    $I->wantTo( 'Test that setting a default form for WooCommerce products will result in that form being shown on a product page.' );
	    $I->amOnPluginsPage();
	    $I->activatePlugin('woocommerce');

	    $I->amOnPage( '/wp-admin/options-general.php?page=_wp_convertkit_settings' );
	    $I->click('Refresh forms');
	    $I->wait(1);
	    $I->selectOption( 'form select[id=product_form]', 'WooCommerce product form' );

	    $I->click('Save Changes');
	    $product_id = $I->havePostInDatabase([
		    'post_type' => 'product',
		    'post_title' => 'Air Jordans',
	    ]);

	    $product = get_post( $product_id );

	    $I->amOnPage( '/product/' . $product->post_name );
	    $I->see( 'Product form title' );
    }
}
