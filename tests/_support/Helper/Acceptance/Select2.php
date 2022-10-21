<?php
namespace Helper\Acceptance;

// Define any custom actions related to Select2 interaction that
// would be used across multiple tests.
// These are then available in $I->{yourFunctionName}

class Select2 extends \Codeception\Module
{
    /**
     * Helper method to enter text into a jQuery Select2 Field, selecting the option that appears.
     * 
     * @since 1.9.6.4
     * 
     * @param AcceptanceTester $I
     * @param string           $container         Field CSS Class / ID
     * @param string           $value             Field Value
     * @param string           $ariaAttributeName Aria Attribute Name (aria-controls|aria-owns)
     */
    public function fillSelect2Field($I, $container, $value, $ariaAttributeName = 'aria-controls')
    {
        $fieldID = $I->grabAttributeFrom($container, 'id');
        $fieldName = str_replace('-container', '', str_replace('select2-', '', $fieldID));
        $I->click('#'.$fieldID);
        $I->waitForElementVisible('.select2-search__field[' . $ariaAttributeName . '="select2-' . $fieldName . '-results"]');
        $I->fillField('.select2-search__field[' . $ariaAttributeName . '="select2-' . $fieldName . '-results"]', $value);
        $I->waitForElementVisible('ul#select2-' . $fieldName . '-results li.select2-results__option--highlighted');
        $I->pressKey('.select2-search__field[' . $ariaAttributeName . '="select2-' . $fieldName . '-results"]', \Facebook\WebDriver\WebDriverKeys::ENTER);
    }

    /**
     * Helper method to open a jQuery Select2 Field.
     * 
     * @since 1.9.8.1
     * 
     * @param AcceptanceTester $I
     * @param string           $container         Field CSS Class / ID
     * @param string           $value             Field Value
     * @param string           $ariaAttributeName Aria Attribute Name (aria-controls|aria-owns)
     */
    public function openSelect2Field($I, $container, $value, $ariaAttributeName = 'aria-controls')
    {
        $fieldID = $I->grabAttributeFrom($container, 'id');
        $fieldName = str_replace('-container', '', str_replace('select2-', '', $fieldID));
        $I->click('#'.$fieldID);
    }
}
