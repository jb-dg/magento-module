<?php

namespace Origines\CatalogWidget\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Cms\Api\BlockRepositoryInterface;

/**
 * Reinjecte les données de position de la table product_position_widget dans les valeurs du widget
 * puis supprime la table
 *
 */
class UpdatePositionsData implements DataPatchInterface
{
    private BlockRepositoryInterface $blockRepository;

    private ModuleDataSetupInterface $moduleDataSetup;

    public function __construct(
    \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup,
        BlockRepositoryInterface $blockRepository
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->blockRepository = $blockRepository;
    }

    public function apply()
    {
        $connection = $this->moduleDataSetup->getConnection();
        $connection->startSetup();
    
        if (!$connection->isTableExists($connection->getTableName('product_position_widget'))) {

            $tableCmsPage = $connection->getTableName('cms_page');
            //On reaffecte les templates aux bons modules
            $query = "UPDATE ".$tableCmsPage." SET content = replace(content,'template=\"product/widget/content/grid.phtml',
                    'template=\"Magento_CatalogWidget::product/widget/content/grid.phtml') WHERE content like '%product/widget/content/grid.phtml%'";
            $connection->query($query);

            $tableCmsBlock = $connection->getTableName('cms_block');
            $query = "UPDATE ".$tableCmsBlock." SET content = replace(content,'template=\"product/widget/content/grid.phtml',
                    'template=\"Magento_CatalogWidget::product/widget/content/grid.phtml') WHERE content like '%product/widget/content/grid.phtml%'";
            $connection->query($query);

            $positionsSkuByBlock = [];
            $table = $connection->getTableName('product_position_widget');
            // remove from product_position_widget where block not exist
            $query = "delete from " . $table. " where block_id not in (select id from ".$tableCmsBlock.")";
            $connection->query($query);

            //On liste les blocs et les positions depuis la base pour les widgets existants du plugin Sutunam\CatalogWidget
            $query = "SELECT product_sku, position, block_id FROM " . $table. " order by block_id";
            $rows = $connection->fetchAll($query);
            foreach ($rows as $row) {
                if (!isset($positionsSkuByBlock[$row['block_id']])) {
                    $positionsSkuByBlock[$row['block_id']] = [];
                }
                $positionsSkuByBlock[$row['block_id']][trim($row['product_sku'])] = $row['position'];
            }

            //On reaffecte les valeurs du nouveau widget a chaque bloc
            foreach ($positionsSkuByBlock as $block_id => $positionSkus){
                $block = $this->blockRepository->getById($block_id);
                if ($block->getId()) {
                    $content = $block->getContent();
                    $newContent = $content;
                    preg_match_all('/{{widget.*Magento\\\CatalogWidget\\\Block\\\Product\\\ProductsList.*`attribute`:`sku`,`operator`:`\(\)`,`value`:`.*`\^],/', $content, $matches);
                    foreach ($matches[0] as $match) {
                        $values = explode('`', $match);
                        $skus = $values[count($values) - 2];
                        $skusList = explode(', ', $skus);
                        $skuAndPosition = [];

                        foreach ($skusList as $sku) {
                            $position = 1;
                            if (isset($positionSkus[$sku])) {
                                $position = $positionSkus[$sku];
                            }
                            $skuAndPosition[] = $sku . ':' . $position;
                        }
                        $skuAndPosition = implode(', ', $skuAndPosition);
                        $newContent = str_replace($skus, $skuAndPosition, $newContent);
                    }
                    $block->setContent($newContent);
                    $block->save();
                }
            }

            $connection->dropTable('product_position_widget');
        }
        $connection->endSetup();
    }


    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
