<?php
namespace Origines\CronExport\Model\Config\Source;

class ListTypePeriod implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'days', 'label' => __('Days')],
            ['value' => 'month', 'label' => __('Month')],
            ['value' => 'year', 'label' => __('Year')],
        ];
    }
}