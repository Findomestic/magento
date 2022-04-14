<?php


namespace Findomestic\MagentoRedirectPay\Block\Checkout;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Findomestic\MagentoRedirectPay\Helper\ConfigData;

class Failure extends Template
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    private $helper;

    public function __construct(
        ConfigData $helper,
        OrderRepositoryInterface $orderRepository,
        Context $context, array $data = [])
    {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->orderRepository = $orderRepository;
    }

    public function getLogo(){
        $image = $this->helper->getLogo();
        return $this->getViewFileUrl('Findomestic_MagentoRedirectPay::images/'.$image);
    }


    /**
     * @param $orderId
     * @return false|OrderInterface
     */
    public function getOrderById($orderId)
    {
        try {
            $order = $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $exception) {
            return false;
        }
        return $order;
    }

    public function getCmsBlock($identifier)
    {
        $cmsBlock = $this->_layout->createBlock(\Magento\Cms\Block\Block::class)
            ->setBlockId($identifier)
            ->toHtml();
        return $cmsBlock;
    }
}
