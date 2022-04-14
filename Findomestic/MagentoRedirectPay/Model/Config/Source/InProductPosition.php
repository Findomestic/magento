<?php

namespace Findomestic\MagentoRedirectPay\Model\Config\Source;

class InProductPosition implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'after', 'label' => __('Dopo "Aggiungi al carrello"')],
            ['value' => 'before', 'label' => __('Dopo il prezzo')],
        ];
    }
}
