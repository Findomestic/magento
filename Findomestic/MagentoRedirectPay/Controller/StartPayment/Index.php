<?php
namespace Findomestic\MagentoRedirectPay\Controller\StartPayment;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Findomestic\MagentoRedirectPay\Setup\Patch\Data;
use Findomestic\MagentoRedirectPay\Helper\ConfigData;
use Findomestic\MagentoRedirectPay\Plugin\FPEncodeDecode;


class Index extends Action
{
    /** @var Session */
    private $checkoutSession;

    private $helper;

    private $fpEncodeDecode;

    private $logger;

    /**
     * @param Context $context
     * @param CreateHostedCheckout $hostedCheckout
     * @param Session $checkoutSession
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        FPEncodeDecode $fpEncodeDecode,
        LoggerInterface $logger,
        ConfigData $helper
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->fpEncodeDecode = $fpEncodeDecode;

    }

    /**
     * Initialize redirect to bank
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        //GET REDIRECT URL
        $url = $this->findomestiBuildRedirectUrl();

        $resultRedirect->setUrl($url);

        return $resultRedirect;
    }

    private function findomestiBuildRedirectUrl(){
        /** @var Order $order */
        $order = $this->checkoutSession->getLastRealOrder();
        $sessionId = $this->checkoutSession->getSessionId();
        $mode = $this->helper->getConfig('paymentMode');

        $orderId = $order->getId();

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $om->get('Magento\Customer\Model\Session');

        $baseUrl = $this->getBaseUrl();

        $order->setState("processing")->setStatus("findomestic_pending");
        $order->save();

        $toEncode = array(
            'key' => $sessionId,
            'id_order' => $orderId
        );

        $encoded = $this->fpEncodeDecode->encodeUrlArgs($toEncode);

        $user_data = array(
            'nomeCliente' => $order->getCustomerFirstname(),
            'cognomeCliente' => $order->getCustomerLastname(),
            'emailCliente' => $order->getCustomerEmail(),
        );


        $urlRedirect = $baseUrl.'magentoredirectpay/endpayment?encoded='.$encoded;
        $callBackUrl = $baseUrl.'magentoredirectpay/update?encoded='.$encoded;

        $url = $this->helper->getWebAppUrl($mode, $order->getTotalDue(), false, $orderId, $user_data, $urlRedirect, $callBackUrl);

        //https://b2ctest.ecredit.it/clienti/pmcrs/eprice/mcommerce/pages/simulatore.html?tvei=123123&prf=7079&nomeCliente=TEST&cognomeCliente=Doing&emailCliente=francesco.mura@doing.com&importo=38940&cartId=6&urlRedirect=SITE_URL/fp/return?encoded=key:9209b2cb631d5115828591fecde44c65-id_order:6-id_cart:6-id_module:70&callBackUrl=SITE_URL/en/fp/update?encoded=key:9209b2cb631d5115828591fecde44c65-id_order:6
        //$this->logger->log(100, json_encode($options));
        $this->logger->debug('REDIRECT: ' . $url);

        return $url;

    }

    private function getBaseUrl(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');

        return $storeManager->getStore()->getBaseUrl();
    }

}
