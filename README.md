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

### Type 1: Zip file

 - Unzip the zip file in `app/code/FlexShopper`
 - Enable the module by running `php bin/magento module:enable FlexShopper_Payments`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require flexshopper/module-payments`
 - enable the module by running `php bin/magento module:enable FlexShopper_Payments`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration

 - FlexShopperPayments - payment/flexshopperpayments/*


## Specifications

 - Payment Method
	- FlexShopperPayments


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
