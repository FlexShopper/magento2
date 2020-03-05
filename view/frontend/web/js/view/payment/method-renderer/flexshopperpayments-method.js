define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery',
        'FlexShopper_Payments/js/view/payment/adapter',
        'Magento_Checkout/js/model/quote',
    ],
    function (Component, $, adapter, quote) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'FlexShopper_Payments/payment/flexshopperpayments',
                code: 'flexshopper',
                active: true,
                grandTotalAmount: null,
                isReviewRequired: false,
            },

            /**
             * Initialize view.
             *
             * @return {exports}
             */
            initialize: function () {
                var self = this;

                self._super();

                var script = document.createElement('script');
                script.type = 'text/javascript';
                script.src = 'https://pp3.flexshopper.com/sdk/js?authKey='+adapter.getAuthKey();
                script.onload = function () {
                    alert('LOADED TODO');
                    FlexSDK.Button({
                        createOrder: function() {},
                        onSign: function() {}
                    }).render('#elementSelector');
                };
                script.onerror = function(e) {
                    alert('Failed to load FlexShopper');
                    console.log(e);
                };

                document.head.appendChild(script);

                return self;
            },

            /**
             * Set list of observable attributes
             * @returns {exports.initObservable}
             */
            initObservable: function () {
                var self = this;

                this._super()
                    .observe(['active', 'isReviewRequired', 'customerEmail']);

                this.grandTotalAmount = quote.totals()['base_grand_total'];

                quote.totals.subscribe(function () {
                    if (self.grandTotalAmount !== quote.totals()['base_grand_total']) {
                        self.grandTotalAmount = quote.totals()['base_grand_total'];
                    }
                });

                quote.shippingAddress.subscribe(function () {
                    if (self.isActive()) {
                        self.reInitFlexShopper();
                    }
                });

                return this;
            },

            /**
             * Check if payment is active
             *
             * @returns {Boolean}
             */
            isActive: function () {
                var active = this.getCode() === this.isChecked();

                this.active(active);

                return active;
            },
        });
    }
);
