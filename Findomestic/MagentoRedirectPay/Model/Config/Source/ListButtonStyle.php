<?php

namespace Findomestic\MagentoRedirectPay\Model\Config\Source;

class ListButtonStyle implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'No', 'label' => __('Nascondi')],
            /*
        ['value' => 'horizontal', 'label' => __('Riga singola')],
        ['value' => 'vertical', 'label' => __('Righe multiple')],
        ['value' => 'horizontal-long', 'label' => __('Riga singola con testo')],
        ['value' => 'vertical-long', 'label' => __('Righe multiple con testo')],
        ['value' => 'no-button-horizontal-long', 'label' => __('Riga singola solo testo (no bottone)')],
        ['value' => 'no-button-vertical-long', 'label' => __('Righe multiple solo testo (no bottone)')],*/
            ['value' => 'no-button-no-bg-horizontal-long', 'label' => __('Riga singola solo testo (no bottone - no container)')],
            ['value' => 'no-button-no-bg-vertical-long', 'label' => __('Righe multiple solo testo (no bottone - no container)')],
        ];
    }
}
