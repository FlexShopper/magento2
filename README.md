# Mage2 Module FlexShopper Payments

    ``flexshopper/module-payments``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
Integration with FlexShopper payment solutions

## Installation
\* = in production please use the `--keep-generated` option

We recommend a staging/development site and try installation on it before installing the extension on the production site.

Make sure you have a backup of the Magento files and database before proceeding.

### Type 1: Zip file

 - Uncompress the archive file in `app/code/FlexShopper/Payments`
 - Enable the module by running `php bin/magento module:enable FlexShopper_Payments`
 - Apply database updates by running `php bin/magento setup:upgrade` \*
 - Regenerate static content by running `php bin/magento setup:static-content:deploy`

### Type 2: Composer

 - Install the module composer by running `composer require flexshopper/magento2`
 - enable the module by running `php bin/magento module:enable FlexShopper_Payments`
 - apply database updates by running `php bin/magento setup:upgrade` \*
 - Regenerate static content by running `php bin/magento setup:static-content:deploy`

## Configuration

 - FlexShopperPayments - payment/flexshopperpayments/*


## Specifications

 - Payment Method
	- FlexShopperPayments

No invoice will be created for Flex Shopper orders and in case the merchant needs a payment document in Magento, they have to manually do it upon payment from FlexShopper, as there is no automated way of getting notified.

## Attributes

 - Product - FlexShopper Leasing Enabled (flexshopper_leasing_enabled)
 
## Brand

Brand is a required parameter for FlexShopper. This extension defaults to "manufacturer", but you can use any attribute
from the configuration. Just make sure it's available for the quote item by adding this to your custom extension:
/etc/catalog_attributes.xml:
```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Catalog:etc/catalog_attributes.xsd">
    <group name="quote_item">
        <attribute name="your_brand_attribute"/>
    </group>
</config>
```

## Unit tests.

Please refer to: https://devdocs.magento.com/guides/v2.3/test/unit/unit_test_execution_phpstorm.html

Or from the command line: `vendor/bin/phpunit -c dev/tests/unit/phpunit.xml`
