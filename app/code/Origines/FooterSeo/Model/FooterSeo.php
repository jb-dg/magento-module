<?php

namespace Origines\FooterSeo\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Origines\FooterSeo\Model\ResourceModel\FooterSeo as FooterSeoResourceModel;

class FooterSeo extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'origines_footer_seo';

    /**
     * Construct
     */
    protected function _construct()
    {
        $this->_init(FooterSeoResourceModel::class);
    }

    /**
     * Get identities
     *
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
