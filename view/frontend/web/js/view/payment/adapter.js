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
