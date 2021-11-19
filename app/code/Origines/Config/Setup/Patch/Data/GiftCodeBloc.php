<?php

namespace Origines\Config\Setup\Patch\Data;

use Origines\Config\Setup\Patch\AbstractPatchData;

class GiftCodeBloc extends AbstractPatchData
{
    public function apply()
    {
        $this->dataSetup->getConnection()->startSetup();

        $cmsBlocks = [
            'gift-code'
        ];

        $this->tbdCmsBlock->saveCmsBlock($cmsBlocks, 'Origines_Config');
        $this->dataSetup->getConnection()->endSetup();
    }
}
