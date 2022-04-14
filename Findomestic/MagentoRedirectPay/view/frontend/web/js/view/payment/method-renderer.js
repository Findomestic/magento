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
                type: 'findomesticpayment',
                component: 'Findomestic_MagentoRedirectPay/js/view/payment/method-renderer/findomesticpayment'
            }
        );
        return Component.extend({});
    }
);
