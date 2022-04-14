<?php

namespace Findomestic\MagentoRedirectPay\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Exception;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;

class UpgradeData implements UpgradeDataInterface
{
    private $statusFactory;
    private $statusResourceFactory;

    public function __construct(StatusFactory $statusFactory, StatusResourceFactory $statusResourceFactory)
    {
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
    }


    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '2.0.6', '<')) {
            $this->addNewOrderProcessingStatus('findomestic_preapproved', 'Findomestic Richiesta in valutazione');
            $this->addNewOrderProcessingStatus('findomestic_pending', 'Findomestic Inserimento dati in corso');
            $this->addNewOrderProcessingStatus('findomestic_accepted', 'Findomestic Richiesta accolta', Order::STATE_COMPLETE);
            $this->addNewOrderProcessingStatus('findomestic_denied', 'Findomestic Richiesta non accolta', Order::STATE_CANCELED);
        }

        $installer->endSetup();
    }

    protected function addNewOrderProcessingStatus($statusCode, $label, $state = Order::STATE_PROCESSING)
    {
        /** @var StatusResource $statusResource */
        $statusResource = $this->statusResourceFactory->create();
        /** @var Status $status */
        $status = $this->statusFactory->create();
        $status->setData([
            'status' => $statusCode,
            'label' => $label,
        ]);
        try {
            $statusResource->save($status);
        } catch (AlreadyExistsException $exception) {
            return;
        }
        $status->assignState($state, false, true);
    }


}
