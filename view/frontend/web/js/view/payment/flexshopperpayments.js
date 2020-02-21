define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'flexshopperpayments',
                component: 'FlexShopper_Payments/js/view/payment/method-renderer/flexshopperpayments-method'
            }
        );
        return Component.extend({});
    }
);