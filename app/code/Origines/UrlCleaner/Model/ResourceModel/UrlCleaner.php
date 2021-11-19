<?php
namespace Origines\UrlCleaner\Model\ResourceModel;

class UrlCleaner extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('url_rewrite', 'url_rewrite_id');
    }

    protected function _initUniqueFields()
    {
        $this->_uniqueFields = [
            ['field' =>
                ['request_path', 'store_id', 'type_id', 'entity_type','entity_id'],
                'title' => __('Request Path for Specified Store')
            ],
        ];
        return $this;
    }

    protected function _getLoadSelect($field, $value, $object)
    {
        /** @var $select \Magento\Framework\DB\Select */
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($object->getStoreId() !== null) {
            $select->where(
                'store_id IN(?)',
                [\Magento\Store\Model\Store::DEFAULT_STORE_ID, $object->getStoreId()]
            );
            $select->order('store_id ' . \Magento\Framework\DB\Select::SQL_DESC);
            $select->limit(1);
        }

        return $select;
    }
}
