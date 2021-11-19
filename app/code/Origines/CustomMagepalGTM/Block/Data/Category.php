<?php
namespace Origines\CustomMagepalGTM\Block\Data;

use Magento\Framework\Exception\LocalizedException;
use MagePal\GoogleTagManager\Model\DataLayerEvent;

class Category extends \Origines\CustomMagepalGTM\Block\CatalogLayer
{

    /**
     * Add category data to datalayer
     *
     * @return $this
     * @throws LocalizedException
     */
    protected function _dataLayer()
    {
        if ($this->_eeHelper->isListTypeCategoryName() && $categoryName = $this->getCurrentCategoryName()) {
            $this->setListType($categoryName);
        } elseif ($list = $this->_eeHelper->getCategoryListType()) {
            $this->setListType($list);
        }

        $collection = $this->_getProducts();

        if (is_object($collection) && $collection->count()) {
            $this->setImpressionList(
                $this->getListType(),
                $this->_eeHelper->getCategoryListClassName(),
                $this->_eeHelper->getCategoryListContainerClass()
            );

            $products = $this->getProductImpressions($collection);

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
}
