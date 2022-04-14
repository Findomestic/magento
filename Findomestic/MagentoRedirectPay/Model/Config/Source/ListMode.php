<?php
// CLASS FOR CUSTOM FIELDS VALUES

namespace Findomestic\MagentoRedirectPay\Model\Config\Source;

class ListMode implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'live', 'label' => __('Live')],
            ['value' => 'test', 'label' => __('Test')],
        ];
    }
}
