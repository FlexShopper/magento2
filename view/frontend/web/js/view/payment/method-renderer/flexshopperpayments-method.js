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
                            const shippingMethod = quote.shippingMethod();
                            const items = quote.getItems();
                            const brandAttr = adapter.getBrandAttr();
                            let flexItems = [];
                            let item;
                            let index;
                            let brand;
                            for (index = 0; index < items.length; ++index) {
                                item = items[index];
                                if (item[brandAttr]) {
                                    brand = item[brandAttr];
                                } else {
                                    brand = 'n/a';
                                }
                                flexItems.push({
                                    description: item.name,
                                    sku: item.sku,
                                    cost: parseFloat(item.price_incl_tax),
                                    brand: brand,
                                    condition: "new",
                                    quantity: item.qty,
                                    images: [ // optional
                                        item.thumbnail
                                    ],
                                    shipping: {
                                        cost: shippingMethod.amount,
                                        date: new Date().toString(),
                                        method: 'ground'
                                    }
                                });
                            }
                            return actions.transaction.create({
                                cost: self.grandTotalAmount,
                                transactionId: quote.getQuoteId(),
                                items: flexItems,
                            });
                        },
                        onSign: function (data) {
                            return fetch(adapter.getValidateUrl(), {
                                method: 'POST',
                                body: JSON.stringify(data)
                            })
                                .then((response) => response.json())
                                .then((data) => {
                                    if (data['valid']) {
                                        self.placeOrder();
                                    } else {
                                        console.log(data);
                                    }
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
            }
        });
    }
);
