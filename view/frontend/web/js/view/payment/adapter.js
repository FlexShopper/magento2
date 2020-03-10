/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define([
    'jquery',
], function ($) {
    'use strict';

    return {
        apiClient: null,
        checkout: null,
        code: 'flexshopper',

        getAuthKey: function () {
            return window.checkoutConfig.payment[this.code].authKey;
        },

        getUrl: function () {
            let url = 'https://pp3.flexshopper.com/sdk/js'; // production
            if (window.checkoutConfig.payment[this.code].mode === 'sandbox') {
                url = 'https://pp3.sandbox.flexshopper.com/sdk/js';
            }
            return url;
        },

        /**
         * Returns payment code
         *
         * @returns {String}
         */
        getCode: function () {
            return this.code;
        },
    };
});
