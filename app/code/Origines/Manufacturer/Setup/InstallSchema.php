<?php

namespace Origines\Manufacturer\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Origines\Manufacturer\Model\ResourceModel\Manufacturer;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @inheritdoc
     * @throws \Zend_Db_Exception
     */
    // @codingStandardsIgnoreLine(Generic.CodeAnalysis.UnusedFunctionParameter)
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable(Manufacturer::TABLE_NAME)
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
            'Entity ID'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Manufacturer name'
        )->addColumn(
            'image',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Manufacturer image'
        )->addColumn(
            'layout',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Manufacturer layout'
        )->addColumn(
            'category',
            Table::TYPE_INTEGER,
            10,
            [
                'unsigned' => true,
                'nullable' => true
            ],
            'Category'
        )->addIndex(
            $setup->getIdxName(
                Manufacturer::TABLE_NAME,
                'name',
                AdapterInterface::INDEX_TYPE_INDEX
            ),
            'name',
            ['type' => AdapterInterface::INDEX_TYPE_INDEX]
        )->addIndex(
            $setup->getIdxName(
                Manufacturer::TABLE_NAME,
                'category',
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            'category',
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addForeignKey(
            $setup->getFkName(Manufacturer::TABLE_NAME, 'category', 'catalog_category_entity', 'entity_id'),
            'category',
            $setup->getTable('catalog_category_entity'),
            'entity_id',
            Table::ACTION_CASCADE
        );

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
