<?php

namespace Findomestic\MagentoRedirectPay\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;

class OrderCancelled extends Action
{
    const ADMIN_RESOURCE = 'Magento_Sales::sales_order';
    /**
     * @var OrderCollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * ChangeColor constructor.
     * @param Action\Context $context
     * @param OrderCollectionFactory $orderCollectionFactory
     */
    public function __construct(
        Action\Context $context,
        OrderCollectionFactory $orderCollectionFactory
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        parent::__construct($context);
    }


    public function execute()
    {
        $request = $this->getRequest();

        $orderIds = $request->getPost('selected', []);
        if (empty($orderIds)) {
            $this->getMessageManager()->addErrorMessage(__('No orders found.'));
            return $this->_redirect('sales/order/index');
        }
        //print_r(orderIds) // Selected Order Ids
        $orderCollection = $this->orderCollectionFactory->create();
        $orderCollection->addFieldToFilter('entity_id', ['in' => $orderIds]);
        try {

            //Add you logic
            foreach ($orderCollection as $order) {
                $order->setState("canceled")->setStatus("findomestic_denied");
                $order->save();
            }
        } catch (\Exception $e) {
            $message = "An unknown error occurred while changing selected orders.";
            $this->getMessageManager()->addErrorMessage(__($message));
        }
        return $this->_redirect('sales/order/index');
    }
}
