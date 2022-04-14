<?php

namespace Findomestic\MagentoRedirectPay\Block;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Findomestic\MagentoRedirectPay\Helper\ConfigData;

class FindomesticCart extends \Magento\Framework\View\Element\Template
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


    public function getCmsBlock($identifier)
    {
        $cmsBlock = $this->_layout->createBlock(\Magento\Cms\Block\Block::class)
            ->setBlockId($identifier)
            ->toHtml();
        return $cmsBlock;
    }

    public function findomesticInProduct($amount){
        return $this->helper->getFrontendOption('inProduct', $amount);
    }


    public function findomesticInCart($amount){
        return $this->helper->getFrontendOption('inCart', $amount);
    }

    public function findomesticSimulatorUrl($price){
        return $this->helper->getWebAppUrl($this->mode, $price, 1);
    }

}
