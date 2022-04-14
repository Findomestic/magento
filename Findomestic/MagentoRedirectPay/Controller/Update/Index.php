<?php
namespace Findomestic\MagentoRedirectPay\Controller\Update;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Findomestic\MagentoRedirectPay\Setup\Patch\Data;
use Findomestic\MagentoRedirectPay\Plugin\FPEncodeDecode;


class Index extends Action{
    /** @var Session */
    private $checkoutSession;

    private $fpEncodeDecode;

    private $successUrl = '/checkout/onepage/success/';

    private $failureUrl = '/magentoredirectpay/findomesticerror/view/';

    private $jsonFactory;

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
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->checkoutSession = $checkoutSession;
        $this->fpEncodeDecode = $fpEncodeDecode;
        $this->orderRepository = $orderRepository ?: ObjectManager::getInstance()->get(OrderRepositoryInterface::class);
        $this->jsonFactory = $jsonFactory;
        $this->logger = $logger;
    }

    /**
     * Initialize redirect to bank
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->logger->debug('FINDOMESTIC CALLBACK: '.json_encode($_GET));
        if($this->updateOrder()){
            $data = ['result' => 'order updated'];
            $result = $this->jsonFactory->create();
            $result->setData($data);
            return $result;
        }else{
            $data = ['result' => 'no data available'];
            $result = $this->jsonFactory->create();
            $result->setData($data);
            return $result;
        }


    }

    private function updateOrder(){
        $encoded = $this->getRequest()->getParam('encoded');

        $esito = $this->getRequest()->getParam('codiceEsitoFindomestic');

        $unencoded = $this->unencode($encoded);

        if ($unencoded == false) {
            return false;
        }

        $order = $this->getOrder($unencoded['id_order']);

        // IMPORTANT: contrary to most cases, here, 0 is success, any other case is denied/failure
        if($esito == '0'){
            $order->setState("paiment_review")->setStatus("findomestic_preapproved");
            $order->save();
        } else{
            $order->setState("canceled")->setStatus("findomestic_denied");
            $order->save();
        }

        return true;

    }

    private function getOrder($orderId){
        return $this->orderRepository->get($orderId);
    }

    private function unencode($encoded){
        return $this->fpEncodeDecode->unencode($encoded);
    }
}
