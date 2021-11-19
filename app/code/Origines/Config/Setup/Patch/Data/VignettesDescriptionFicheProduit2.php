<?php
/*
 * Copyright (c) 2014 - 2021 TBD, SAS. All rights reserved.
 * Developer: Pierre-Louis HUBERT <pierre-louis.hubert@tbdgroup.com>
 */


namespace Origines\Config\Setup\Patch\Data;

use Origines\Config\Setup\Patch\AbstractPatchData;

class VignettesDescriptionFicheProduit2 extends AbstractPatchData
{
    public function apply()
    {
        $this->dataSetup->getConnection()->startSetup();

        $cmsBlocks = [
            'vignettes_description'
        ];

        $this->tbdCmsBlock->saveCmsBlock($cmsBlocks, 'Origines_Config');
        $this->dataSetup->getConnection()->endSetup();
    }
}
