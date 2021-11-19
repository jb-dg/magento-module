<?php

namespace Origines\FooterSeo\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class FooterSeo extends AbstractDb
{
    const TABLE_NAME = 'origines_footer_seo';

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'entity_id');
    }
}
