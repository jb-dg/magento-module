<?php

namespace Origines\FooterSeo\Model\ResourceModel\FooterSeo;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Origines\FooterSeo\Model\FooterSeo as FooterSeoModel;
use Origines\FooterSeo\Model\ResourceModel\FooterSeo as FooterSeoResourceModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'origines_footerseo_footerseo_collection';
    protected $_eventObject = 'footerseo_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(FooterSeoModel::class, FooterSeoResourceModel::class);
    }
}
