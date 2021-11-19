/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, uiRegistry, select) {
    'use strict';

    return select.extend({

        /**
         * Hide fields on coupon tab
         */
        onUpdate: function (value) {
            var field1 = uiRegistry.get('index = category_id');
            if (field1.visibleValue == value){
                field1.show();
            }else{
                field1.hide();
            }

            var field2 = uiRegistry.get('index = product_sku');
            if (field2.visibleValue == value){
                field2.show();
            }else{
                field2.hide();
            }
        },
    });
});
