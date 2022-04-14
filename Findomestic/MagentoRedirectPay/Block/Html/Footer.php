<?php

namespace Findomestic\MagentoRedirectPay\Block\Html;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Findomestic\MagentoRedirectPay\Helper\ConfigData;

class Footer extends Template
{
    private $helper;

    private $mode;

    public function __construct(
        ConfigData $helper,
        Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->mode = $this->helper->getConfig('paymentMode');
    }


    public function getConfig($field, $path = 'payment/findomesticpayment/')
    {
        return $this->helper->getConfig($field, $path);
    }

    public function getMode(){
        return $this->mode;
    }

    public function getlegalDisclaimer(){
        return $this->getConfig('legalDisclaimer', 'payment/findomesticpayment_'.$this->getMode().'/');
    }

}
