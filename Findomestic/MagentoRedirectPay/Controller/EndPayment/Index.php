<?php
namespace Findomestic\MagentoRedirectPay\Controller\EndPayment;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order;
use Findomestic\MagentoRedirectPay\Setup\Patch\Data;
use Findomestic\MagentoRedirectPay\Plugin\FPEncodeDecode;

class Index extends Action{
    /** @var Session */
    private $checkoutSession;

    private $fpEncodeDecode;

    private $successUrl = '/checkout/onepage/success/';

    private $failureUrl = '/magentoredirectpay/findomesticerror/view/';

    private $logger;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

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
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->fpEncodeDecode = $fpEncodeDecode;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository ?: ObjectManager::getInstance()->get(OrderRepositoryInterface::class);
    }

    /**
     * Initialize redirect to bank
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->logger->debug('USER RETURN: '.json_encode($_GET));
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        //GET REDIRECT URL
        $url = $this->findomestiBuildFinalRedirectUrl();

        $resultRedirect->setUrl($url);

        return $resultRedirect;
    }

    private function findomestiBuildFinalRedirectUrl(){
        $encoded = $this->getRequest()->getParam('encoded');

        $sessionId = $this->checkoutSession->getSessionId();

        $esito = $this->getRequest()->getParam('esito');

        $unencoded = $this->unencode($encoded);

        $order = $this->getOrder($unencoded['id_order']);

        if(strtoupper($esito) == 'OK'){
            $currentStatus = $order->getStatus();
            if($sessionId == $unencoded['key']){
                $order->setState("paiment_review")->setStatus("findomestic_preapproved");
                $order->save();
                return $this->successUrl;
            }
            return $this->failureUrl;

        } else{
            $order->setState("canceled")->setStatus("findomestic_denied");
            $order->save();
            return $this->failureUrl;
        }

    }

    private function getOrder($orderId){
        return $this->orderRepository->get($orderId);
    }

    private function unencode($encoded){
        return $this->fpEncodeDecode->unencode($encoded);
    }
}
