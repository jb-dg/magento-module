<?php

namespace Origines\Config\Setup\Patch\Data;

use Origines\Config\Setup\Patch\AbstractPatchData;

class ProductShipBloc extends AbstractPatchData
{
    public function apply()
    {
        $this->dataSetup->getConnection()->startSetup();

        $cmsBlocks = [
            'product-ship-package',
        ];

        $this->tbdCmsBlock->saveCmsBlock($cmsBlocks, 'Origines_Config');
        $this->dataSetup->getConnection()->endSetup();
    }
}
