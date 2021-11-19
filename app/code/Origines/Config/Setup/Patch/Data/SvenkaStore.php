<?php

namespace Origines\Config\Setup\Patch\Data;

use Origines\Config\Setup\Patch\AbstractPatchData;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Setup\Patch\NonTransactionableInterface;

/** 
    NonTransactionableInterface is important to be able to have sequence created for a store
    So exception handling must be done "manually" like in Magento\Framework\Setup\Patch\PatchApplier
**/
class SvenkaStore extends AbstractPatchData implements NonTransactionableInterface
{

    public function apply()
    {
        // Start Setup        
        $this->dataSetup->getConnection()->startSetup();

        try {
            // Store sould have been created here too 
            $website = $this->tbdWebsite->getDefaultWebsite();
            $storeGroup = $this->tbdStoreGroup->getDefaultGroup();
            $storeData = [
                'se' => [
                    'name'          => 'Svenka',
                    'website_id'    => $website->getId(),
                    'group_id'      => $storeGroup->getId(),
                    'sort_order'    => 0,
                    'is_active'     => 1
                ]
            ];
            $createdStores = $this->tbdStoreview->createStoreview($storeData);
        
            // Add specific store configurations
            $store = $createdStores['se'];
            $config = [
                ScopeInterface::SCOPE_STORES => [
                    $store->getId() => [
                        'general/locale/code' => 'sv_SE',
                    ]
                ]
            ];
            $this->tbdConfig->saveConfig($config);

        } catch (\Exception $e) {
            throw new SetupException(
                new Phrase('
                    Unable to apply data patch %1 for module %2. Original exception message: %3',
                    [
                        get_class($dataPatch),
                        $moduleName,
                        $e->getMessage()
                    ]
                ),
                $e
            );
        }
        // End Setup        
        $this->dataSetup->getConnection()->endSetup();
    }
}
