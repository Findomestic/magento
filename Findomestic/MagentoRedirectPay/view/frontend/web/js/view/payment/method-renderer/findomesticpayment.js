define(
    [
        'Magento_Checkout/js/view/payment/default',
        'Magento_Checkout/js/action/redirect-on-success',
        'mage/url',
        'Magento_Checkout/js/model/quote',
    ],
    function (Component, redirectOnSuccessAction, url, quote) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Findomestic_MagentoRedirectPay/payment/findomestic'
            },
            afterPlaceOrder: function () {
                //redirectOnSuccessAction.redirectUrl = url.build('magentoredirectpay/startpayment/'+quote.getQuoteId());
                redirectOnSuccessAction.redirectUrl = url.build('magentoredirectpay/startpayment/');
                this.redirectAfterPlaceOrder = true;
            },
            getFindomesticLogo: function (){
                var xmlHttp = null;

                xmlHttp = new XMLHttpRequest();
                xmlHttp.open( "GET", '/magentoredirectpay/getlogo', false );
                xmlHttp.send( null );

                var iconJson = jQuery.parseJSON(xmlHttp.responseText);
                var icon = iconJson.icon;

                /*
                xmlHttp.open( "GET", theUrl, false );
                xmlHttp.send( null );
                return xmlHttp.responseText;
                var jsonIcon = jQuery.getJSON('/magentoredirectpay/getlogo', function( data ) {


                });
                console.log(jsonIcon);
                console.log(jsonIcon.responseJSON);
                console.log(jsonIcon.responseJSON.icon);
                var icon = jsonIcon.responseJSON.icon;

                 */
                return 'Findomestic_MagentoRedirectPay/images/'+icon;

            }
        });

    }
);
