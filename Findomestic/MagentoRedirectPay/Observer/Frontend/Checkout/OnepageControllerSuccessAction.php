<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Findomestic\MagentoRedirectPay\Observer\Frontend\Checkout;

use Magento\Framework\Event\Observer;
use Magento\Framework\View\LayoutInterface;

class OnepageControllerSuccessAction implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    public function __construct(LayoutInterface $layout)
    {
        $this->layout = $layout;
    }
    /**
     * Execute observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(
        Observer $observer
    ) {
        $orderIds = $observer->getEvent()->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }
        $block = $this->layout->getBlock('checkout.success.custom.block');
        if ($block) {
            $block->setOrderIds($orderIds);
        }
    }
}
