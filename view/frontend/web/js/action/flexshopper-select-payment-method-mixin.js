
define([
    'jquery',
    'mage/utils/wrapper',
], function ($, wrapper) {
    'use strict';

    return function (selectPaymentMethodAction) {

        return wrapper.wrap(selectPaymentMethodAction, function (originalSelectPaymentMethodAction, paymentMethod) {

            originalSelectPaymentMethodAction(paymentMethod);
            const flexshopperButton = $('#flexshopper-checkout-button');
            if (paymentMethod.method !== 'flexshopper' && flexshopperButton.length) {
                flexshopperButton.hide();
                $('button.checkout').show();
            }
        });
    };

});
