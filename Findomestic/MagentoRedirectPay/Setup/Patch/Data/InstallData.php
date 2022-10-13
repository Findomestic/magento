<?php
/* File: app/code/Findomestic/MagentoRedirectPay/Setup/Patch/Data/InstallData.php */
namespace Findomestic\MagentoRedirectPay\Setup\Patch\Data;

use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;

/**
 * Class InstallData
 * @package Findomestic\MagentoRedirectPayment\Setup\Patch\Data
 */
class InstallData implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    /**
     * Status Factory
     *
     * @var StatusFactory
     */
    protected $statusFactory;
    /**
     * Status Resource Factory
     *
     * @var StatusResourceFactory
     */
    protected $statusResourceFactory;



    /**
     * InstallData constructor
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
    }


    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        /**
         * Prepare database for install
         */
        $this->moduleDataSetup->getConnection()->startSetup();

        $quoteInstaller = $this->quoteSetupFactory->create();
        $salesInstaller = $this->salesSetupFactory->create();

        $statuses = [
            'findomestic_pending' => __('Findomestic Inserimento dati in corso'),
            'findomestic_denied' => __('Findomestic Richiesta non accolta'),
            'findomestic_preapproved'  => __('Findomestic Richiesta in valutazione'),
            'findomestic_accepted'  => __('Findomestic Richiesta accolta'),
        ];
        foreach ($statuses as $code => $info) {
            $data[] = ['status' => $code, 'label' => $info];
        }
        $this->moduleDataSetup->getConnection()->insertArray(
            $this->moduleDataSetup->getTable('sales_order_status'),
            ['status', 'label'],
            $data
        );

        $this->setEnableOrderStatusFrontend();

        /**
         * Prepare database after install
         */
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    private function setEnableOrderStatusFrontend(){
        $this->addNewOrderProcessingStatus('findomestic_preapproved', 'Findomestic Richiesta in valutazione');
        $this->addNewOrderProcessingStatus('findomestic_pending', 'Findomestic Inserimento dati in corso');
        $this->addNewOrderProcessingStatus('findomestic_accepted', 'Findomestic Richiesta accolta', Order::STATE_COMPLETE);
        $this->addNewOrderProcessingStatus('findomestic_denied', 'Findomestic Richiesta non accolta', Order::STATE_CANCELED);
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getVersion()
    {
        return '2.0.7';
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    protected function addNewOrderProcessingStatus($statusCode, $label, $state = Order::STATE_PROCESSING)
    {
        //sales_order_status

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
        } catch (Exception $exception) {
            return;
        }
        $status->assignState($state, false, true);
    }

}
