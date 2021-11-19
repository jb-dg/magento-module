<?php

namespace Origines\Manufacturer\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Manufacturer extends AbstractDb
{
    const TABLE_NAME = 'origines_manufacturer';

    public function _construct()
    {
        $this->_init(self::TABLE_NAME, 'entity_id');
    }
}
