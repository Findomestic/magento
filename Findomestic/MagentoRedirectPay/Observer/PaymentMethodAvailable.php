<?php
namespace Findomestic\MagentoRedirectPay\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Checkout\Model\Cart;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Findomestic\MagentoRedirectPay\Helper\ConfigData;
use Psr\Log\LoggerInterface;

class PaymentMethodAvailable implements ObserverInterface
{
    private $cart;

    private $scopeConfig;

    private $helper;

    private $logger;

    const MIN_AMOUNT_CONFIG = 'contact/email/recipient_email';

    public function __construct(Cart $cart, ScopeConfigInterface $scopeConfig, LoggerInterface $logger, ConfigData $helper){
        $this->cart = $cart;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->helper = $helper;
    }
    /**
     * payment_method_is_active event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        if($observer->getEvent()->getMethodInstance()->getCode()=="findomesticpayment"){
            $this->checkIfAvailable($observer);
        }
    }

    private function checkIfAvailable(Observer $observer){
        $grandTotal = $this->cart->getQuote()->getGrandTotal();
        $mode = $this->helper->getConfig('paymentMode');
        $this->logger->debug('MODE: ' . $mode);
        if($mode != 'live' && $mode != 'test'){
            $this->disablePaymentMethod($observer);
            return;
        }
        $this->logger->debug('Min amount: ' . $this->helper->getConfig('minAmount', 'payment/findomesticpayment_'.$mode.'/'));
        if($grandTotal < $this->helper->getConfig('minAmount', 'payment/findomesticpayment_'.$mode.'/')){
            $this->disablePaymentMethod($observer);
            return;
        }

        $tvei = $this->helper->getConfig('tvei', 'payment/findomesticpayment_'.$mode.'/');
        $this->logger->debug('tvei: ' . $tvei);
        if($tvei == ''){
            $this->disablePaymentMethod($observer);
            return;
        }

        $prf = $this->helper->getConfig('prf', 'payment/findomesticpayment_'.$mode.'/');
        if($prf == ''){
            $this->disablePaymentMethod($observer);
            return;
        }

        $url = $this->helper->getConfig('findomesticUrl', 'payment/findomesticpayment_'.$mode.'/');
        if($url == ''){
            $this->disablePaymentMethod($observer);
            return;
        }
    }

    private function disablePaymentMethod(Observer $observer){
        $checkResult = $observer->getEvent()->getResult();
        $checkResult->setData('is_available', false); //this is disabling the payment method at checkout page
    }
}
