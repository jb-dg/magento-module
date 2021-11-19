<?php

namespace Origines\Config\Setup\Patch\Data;

use Origines\Config\Setup\Patch\AbstractPatchData;

class HomePageCms extends AbstractPatchData
{
    public function apply()
    {
        $this->dataSetup->getConnection()->startSetup();

        $cmsBlocks = [
            'main-content-home-page',
            'home-reassurance',
            'home-slider-products-1',
            'home-my-selection',
            'home-brand-zone',
            'home-slider-products-2',
            'home-my-magazine',
            'home-slider-products-3',
            'home-slider-brand',
            'home-content-plus',
        ];

        $this->tbdCmsBlock->saveCmsBlock($cmsBlocks, 'Origines_Config');
        $this->dataSetup->getConnection()->endSetup();
    }
}
