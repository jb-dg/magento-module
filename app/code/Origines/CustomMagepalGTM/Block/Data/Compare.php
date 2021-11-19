<?php
namespace Origines\CustomMagepalGTM\Block\Data;

use Magento\Framework\Exception\LocalizedException;
use MagePal\GoogleTagManager\Model\DataLayerEvent;

class Compare extends \Origines\CustomMagepalGTM\Block\CatalogLayer
{
    /**
     * Add category data to datalayer
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _dataLayer()
    {
        if ($list = $this->_eeHelper->getCompareListType()) {
            $this->setListType($list);
        }

        $collection = $this->_getProductCollection();

        if (is_object($collection) && $collection->count()) {
            $products = $this->getProductImpressions($collection);

            $this->setImpressionList(
                $this->getListType(),
                $this->_eeHelper->getCompareListClassName(),
                $this->_eeHelper->getCompareListContainerClass()
            );

            $impressionsData = [
                'event' => DataLayerEvent::PRODUCT_IMPRESSION_EVENT,
                'ecommerce' => [
                    'impressions' => $products,
                    'currencyCode' => $this->getStoreCurrencyCode()
                ]
            ];

            $this->addCustomDataLayerByEvent(DataLayerEvent::PRODUCT_IMPRESSION_EVENT, $impressionsData);
        }

        return $this;
    }

    protected function isRelatedOrCrosssell()
    {
        return true;
    }
}
