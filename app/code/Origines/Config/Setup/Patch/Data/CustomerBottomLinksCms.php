<?php

namespace Origines\Config\Setup\Patch\Data;

use Origines\Config\Setup\Patch\AbstractPatchData;

class CustomerBottomLinksCms extends AbstractPatchData
{
    public function apply()
    {
        $this->dataSetup->getConnection()->startSetup();

        $cmsBlocks = [
            'account-bottom-links'
        ];

        $this->tbdCmsBlock->saveCmsBlock($cmsBlocks, 'Origines_Config');
        $this->dataSetup->getConnection()->endSetup();
    }
}
