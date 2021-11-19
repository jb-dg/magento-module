<?php
namespace Origines\ApiNewsletter\Model\Config\Source;

class CouponCodeFormatList implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Constants which defines all possible coupon codes formats
     */
    const COUPON_FORMAT_ALPHANUMERIC = 'alphanum';
    const COUPON_FORMAT_ALPHABETICAL = 'alpha';
    const COUPON_FORMAT_NUMERIC = 'num';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::COUPON_FORMAT_ALPHANUMERIC, 'label' => __('Alphanumeric')],
            ['value' => self::COUPON_FORMAT_ALPHABETICAL, 'label' => __('Alphabetical')],
            ['value' => self::COUPON_FORMAT_NUMERIC, 'label' => __('Numeric')],
        ];
    }
}
