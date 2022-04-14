<?php
namespace Findomestic\MagentoRedirectPay\Controller\GetLogo;

use Findomestic\MagentoRedirectPay\Helper\ConfigData;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Findomestic\MagentoRedirectPay\Plugin\FPEncodeDecode;


class Index extends Action{
    /** @var Session */

    private $jsonFactory;

    private $helper;

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
        ConfigData $helper,
        LoggerInterface $logger,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->helper = $helper;
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
        $data = ['icon' => $this->helper->getIcon()];
        $result = $this->jsonFactory->create();
        $result->setData($data);
        return $result;
    }

}
