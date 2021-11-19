<?php

namespace Origines\Config\Setup\Patch\Data;

use Origines\Config\Setup\Patch\AbstractPatchData;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

class AddMyoriginesTheme extends AbstractPatchData
{
    const THEME_NAME = 'Myorigines/default';

    public function apply()
    {
        // Start Setup        
        $this->dataSetup->getConnection()->startSetup();

        /**
         *  [
         *      'scope_code' => [
         *          'store_code' => 'theme_code'
         *       ]
         *  ]
         */
        $config = [
            ScopeInterface::SCOPE_WEBSITES => [
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT => self::THEME_NAME
            ]   
        ]; 
        $this->tbdTheme->setTheme($config);

        // End Setup        
        $this->dataSetup->getConnection()->endSetup();
    }
}
