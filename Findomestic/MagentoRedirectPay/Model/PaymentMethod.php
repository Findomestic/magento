<?php

namespace Findomestic\MagentoRedirectPay\Model;

/**
 * Pay In Store payment method model
 */
class PaymentMethod extends \Magento\Payment\Model\Method\AbstractMethod
{
    protected $_canAuthorize = true;
    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'findomesticpayment';
    //protected $_code = 'custompayment';
}
