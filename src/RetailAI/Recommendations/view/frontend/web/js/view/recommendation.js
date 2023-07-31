/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'mage/mage',
    'mage/decorate'
], function (Component, customerData, $) {
    'use strict';

    return Component.extend({
        /** @inheritdoc
         * @param {string} config.recCacheKey - CacheKey
         * @param {string} config.placementId - Google PlacementId
         * @param {string} config.eventType - Google Event Type
         * @param {string} config.productId - Magento ProductId
         * @param {string} config.ajaxUrl - URL
         */
        initialize: function (config) {
            this._super();
            this.recommendation = customerData.get('recommendation');
            //console.log(this.recommendation);
            // @todo use the cache key created in the block :)
            let dataKey = config.recCacheKey,
             htmlData = this.recommendation,
             recHTML = htmlData()[dataKey];

            if (typeof recHTML !== 'undefined') {
                //console.log('FROM LOCAL STORAGE');
                $('#google_recs').html(recHTML);
                return true;
            }
                $(document).ready(function () {
                    $.ajax({
                        context: '#google_recs',
                        url: config.ajaxUrl,
                        type: 'POST',
                        data: {'placement-id': config.placementId,
                            'event-type': config.eventType,
                            'product-id': config.productId,
                            'recommendation-key': dataKey}
                    }).done(function (data) {
                        $('#google_recs').html(data.output);
                        return true;
                    });
                });

        }
    });
});
