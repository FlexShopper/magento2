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
                quoteId: null,
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
                script.src = adapter.getUrl()+'?authKey='+encodeURIComponent(adapter.getAuthKey());
                script.onload = function () {
                    FlexSDK.Button({
                        createOrder: function(data, actions) {
                            console.log(quote.items)
                            return actions.transaction.create({
                                cost: self.grandTotalAmount,
                                transactionId: "ABC-129384",
                                items: [
                                    {
                                        description: "Macbook Pro 13",
                                        sku: "ABC123",
                                        cost: 120.34,
                                        brand: "Apple",
                                        condition: "new",
                                        quantity: 1,
                                        images: [ // optional
                                            "https://images.dog.ceo/breeds/husky/n02110185_11635.jpg"
                                        ],
                                        shipping: {
                                            cost: 10.33,
                                            date: new Date().toString(),
                                            method: "UPS"
                                        }
                                    }
                                ]
                            });
                        },
                        onSign: function(data) {
                            return fetch('/flexshopper/validate-order', {
                                method: 'POST',
                                body: JSON.stringify(data)
                            }).then(function() {
                                self.placeOrder();
                            });
                        }
                    }).render('#flexshopper-button');
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

            clickEvent: function() {
                alert('click');
            }
        });
    }
);
