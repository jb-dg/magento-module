<?php

namespace Origines\CustomNewsletters\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getTable('newsletter_subscriber');

        $setup->getConnection()->addColumn(
            $table,
            'subscriber_firstname',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'subscriber First Name'
            ]
        );
        $setup->getConnection()->addColumn(
            $table,
            'subscriber_lastname',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'subscriber Last Name'
            ]
        );
        $setup->getConnection()->addColumn(
            $table,
            'subscriber_gender',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => true,
                'comment' => 'subscriber Gender'
            ]
        );
        $setup->endSetup();
    }
}
