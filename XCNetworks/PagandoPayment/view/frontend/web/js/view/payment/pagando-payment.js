define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push(
        {
            type: 'pagandoPayment',
            component: 'XCNetworks_PagandoPayment/js/view/payment/method-renderer/pagandoPayment-method'
        }
    );

    /** Add view logic here if needed */
    return Component.extend({});
});