<?php

namespace Origines\Config\Setup\Patch\Data;

use Origines\Config\Setup\Patch\AbstractPatchData;

class MegamenuCms extends AbstractPatchData
{
    public function apply()
    {
        $this->dataSetup->getConnection()->startSetup();

        $cmsBlocks = [
            'megamenu-cms-soins',
			'megamenu-cms-parfums',
			'megamenu-cms-para',
			'megamenu-cms-marques',
			'megamenu-cms-make-up',
			'megamenu-cms-cheveux',
			'megamenu-cms-bplan',
			'megamenu-cms-face-en',
			'megamenu-cms-fragrance-en',
			'megamenu-cms-para-en',
			'megamenu-cms-brands-en',
			'megamenu-cms-make-up-en',
			'megamenu-cms-hair-en',
			'megamenu-cms-body-en',
			'megamenu-cms-face-de',
			'megamenu-cms-fragrance-de',
			'megamenu-cms-para-de',
			'megamenu-cms-brands-de',
			'megamenu-cms-make-up-de',
			'megamenu-cms-hair-de',
			'megamenu-cms-body-de',
			'megamenu-cms-face-it',
			'megamenu-cms-fragrance-it',
			'megamenu-cms-para-it',
			'megamenu-cms-brands-it',
			'megamenu-cms-make-up-it',
			'megamenu-cms-hair-it',
			'megamenu-cms-body-it',
			'megamenu-cms-face-es',
			'megamenu-cms-fragrance-es',
			'megamenu-cms-para-es',
			'megamenu-cms-brands-es',
			'megamenu-cms-make-up-es',
			'megamenu-cms-hair-es',
			'megamenu-cms-body-es',
			'megamenu-cms-face-se',
			'megamenu-cms-fragrance-se',
			'megamenu-cms-para-se',
			'megamenu-cms-brands-se',
			'megamenu-cms-make-up-se',
			'megamenu-cms-hair-se',
			'megamenu-cms-body-se'
        ];

        $this->tbdCmsBlock->saveCmsBlock($cmsBlocks, 'Origines_Config');
        $this->dataSetup->getConnection()->endSetup();
    }
}