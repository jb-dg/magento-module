<?php

namespace Origines\FooterSeo\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable(
            $setup->getTable('origines_footer_seo')
        )->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true, 'unsigned' => true],
            'Entity ID'
        )->addColumn(
            'status',
            Table::TYPE_SMALLINT,
            1
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Footer name'
        )->addColumn(
            'type',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'type view page'
        )->addColumn(
            'type_id',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'id category, id product or id page'
        )->addColumn(
            'store_view',
            Table::TYPE_SMALLINT,
            2,
            ['nullable' => false],
            'id store view'
        )->addColumn(
            'footer_content_text',
            Table::TYPE_TEXT,
            1000,
            ['nullable' => false],
            'footer content text'
        );
        $setup->getConnection()->createTable($table);
        $setup->endSetup();
    }
}
