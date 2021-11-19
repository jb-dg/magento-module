<?php

namespace Origines\Config\Setup\Patch\Data;

use Origines\Config\Setup\Patch\AbstractPatchData;

class CategoryTop extends AbstractPatchData
{
    public function apply()
    {
        $this->dataSetup->startSetup();

        $cmsBlocks = [
            'top-category-470'
        ];

        $this->tbdCmsBlock->saveCmsBlock($cmsBlocks, 'Origines_Config');
        $this->dataSetup->endSetup();
    }
}
