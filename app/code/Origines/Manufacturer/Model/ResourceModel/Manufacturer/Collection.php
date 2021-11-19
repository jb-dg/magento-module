<?php

namespace Origines\Manufacturer\Model\ResourceModel\Manufacturer;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Origines\Manufacturer\Model;

class Collection extends AbstractCollection
{
    public function _construct()
    {
        $this->_init(
            Model\Manufacturer::class,
            Model\ResourceModel\Manufacturer::class
        );
    }

    /**
     *
     * @param string $name
     *
     * @return $this
     */
    public function getByName($name)
    {
        return $this->addFieldToFilter('name', $name);
    }

    public function toOptionArray()
    {
        return parent::_toOptionArray('entity_id');
    }
}
