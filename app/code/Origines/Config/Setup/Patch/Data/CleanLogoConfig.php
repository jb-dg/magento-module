<?php

namespace Origines\Config\Setup\Patch\Data;

use Origines\Config\Setup\Patch\AbstractPatchData;
use TBD\Core\Setup\Tools\Config;

class CleanLogoConfig extends AbstractPatchData
{

    public function apply()
    {
        $this->dataSetup->getConnection()->startSetup();

        $this->tbdConfig->deleteConfig('design/header/logo_src');
        $this->tbdConfig->deleteConfig('design/header/logo_width');
        $this->tbdConfig->deleteConfig('design/header/logo_height');

        $this->dataSetup->getConnection()->endSetup();
    }
}
