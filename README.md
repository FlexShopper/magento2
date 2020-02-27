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

## Unit tests.

Please refer to: https://devdocs.magento.com/guides/v2.3/test/unit/unit_test_execution_phpstorm.html

Or from the command line: `vendor/bin/phpunit -c dev/tests/unit/phpunit.xml`
