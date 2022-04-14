<?php


namespace Findomestic\MagentoRedirectPay\Model\Config\Source;


class LogoColor implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'green', 'label' => __('Verde')],
            ['value' => 'negative', 'label' => __('Bianco')],
        ];
    }
}
