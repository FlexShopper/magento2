# Magento 2 Module FlexShopper Payments

  * [Overview](#overview)
  * [Installation](#installation)
    + [Type 1: Zip file](#type-1--zip-file)
    + [Type 2: Composer](#type-2--composer)
  * [Configuration](#configuration)
    + [Enable the Payment Method](#enable-the-payment-method)
    + [Decide which products can be bought with Flex Shopper](#decide-which-products-can-be-bought-with-flex-shopper)
    + [Conditions for the FlexShopper payment method to show in the frontend](#conditions-for-the-flexshopper-payment-method-to-show-in-the-frontend)
    + [View the FlexShopper payment method in the fronted](#view-the-flexshopper-payment-method-in-the-fronted)
    + [Order processing](#order-processing)
    + [Other integration points](#other-integration-points)
  * [Limitations](#limitations)
  * [FAQ](#faq)

## Overview
Integration with FlexShopper payment solutions. 

The FlexPay Payment Platform is a simple way to enable your users who may not otherwise qualify for financing to securely finance their order through our easy Lease to Own experience.

To start using the extension you must have an approved account with  [FlexShopper](https://www.flexshopper.com/) . Upon approval, you will get two types of keys (auth key and api key) which will ne needed in configuring the extension.


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

You may have to refresh the cache after installation.

## Configuration

### Enable the Payment Method

To enable the payment method go to Stores > Configuration > Sales > Payment Methods and expand the "FlexShopper Payments" tab:

![Settings](doc/settings.png?raw=true "Settings.png")

| Setting | Example Value     |Description|
| ------- | ------------------|-------|
|Enabled  | Yes |Is the payment method enabled?|
|Title    | FlexShopper Payments|The payment method name to display in the frontend|
|Sandbox Mode|No|Whether the payment is in sandbox (test) or production (live) mode. Note that authentication keys are different and will be provided by FlexShopper|
|Auth Key|o3d983j-54a2-asdas-8f27-32423asdd232|Provided by FlexShopper|
|API Key|ewfn32ihd809p2jd90hidfnasmfd|Provided by FlexShopper|
|New Order Status|Pending|The status of a newly placed FlexShopper order|
|Minimum Order Value|500|The minimum order value for which FlexShopper displays. This is just a local cache and the value is usually pulled from FlexShopper. The minimum order amount will be communicated by FlexShopper at account creation time. We recommend setting this value to the same value that FlexShopper agreed for your account|
|Brand Attribute|manufacturer|The product attribute that keeps the brand information for your products|
|Payment from Applicable Countries|All Allowed Countries|Set to restrict FlexShopper to specific countries|
|Payment from Applicable Countries|USA|If the above is set to "Specific Countries", choose the countries here|
|Sort Order|20|Defines the sort order for the payment method in the frontend|
|Debug|Yes|Allows logging debugging information (API calls and responses) for the FlexShopper API|

### Decide which products can be bought with Flex Shopper

Each product will have "FlexShopper Leasing Enabled" Yes/No attribute:

![Product](doc/prod.png?raw=true "Product.png")

You must set this to "Yes" for all products that can be paid with FlexShopper.

### Conditions for the FlexShopper payment method to show in the frontend

- the Payment method must be enabled in the admin
- *all* products in the shopping cart must have "FlexShopper Leasing Enabled" set to "Yes"
- the order must be over the minimum order amount that was agreed with FlexShopper
- no product in the cart must trigger a backorder
- the API keys must be set and be valid
- if country restriction is enabled, the customer's address must be from one of the approved countries


### View the FlexShopper payment method in the fronted

If all above conditions match, the payment method will appear in the frontend and will trigger a popup that will guide the customer in getting a lease and completing the payment:

 ![Payment 2](doc/p2.png?raw=true "P2.png")
 ![Payment 1](doc/p1.png?raw=true "P1.png")


### Order processing

The order view screen will list the FlexShopper payment method:

 ![Order View](doc/order_view.png?raw=true "order_view.png")
 
### Other integration points


The following actions will communicate with FlexShopper, in addition to order placing:

- Cancelling an order will send the cancellation to FlexShopper
- Shipping an order (fully or partially) will send the information to FlexShopper
- (Commerce only) Marking a return as "Received" send the return information to FlexShopper

## Limitations

- The payment method only works in the frontend one page checkout.
- Mixed carts (FlexShopper enabled + FlexShopper disabled products) are not supported. In this case, the payment method will not show.
- No invoice will be created for Flex Shopper orders and in case the merchant needs a payment document in Magento, they have to manually do it upon payment from FlexShopper, as there is no automated way of getting notified.

## Contact us 

If you have any questions please contact us at sales@flexshopper.com
