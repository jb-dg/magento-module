<?php

namespace Origines\Config\Setup\Patch\Data;

use Origines\Config\Setup\Patch\AbstractPatchData;

class FooterCms extends AbstractPatchData
{
    public function apply()
    {
        $this->dataSetup->getConnection()->startSetup();

        $cmsBlocks = [
            'main-footer',
            'main-footer-checkout',
            'footer-reassurance',
            'footer-links',
            'footer-trustpilot'
        ];

        $this->tbdCmsBlock->saveCmsBlock($cmsBlocks, 'Origines_Config');
        $this->dataSetup->getConnection()->endSetup();
    }
}
