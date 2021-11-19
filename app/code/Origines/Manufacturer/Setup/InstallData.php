<?php

namespace Origines\Manufacturer\Setup;

use Magento\Catalog\Model\Category;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface
{
    /** @var EavSetupFactory $eavSetupFactory */
    private $eavSetupFactory;

    /**
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    // @codingStandardsIgnoreLine(Generic.CodeAnalysis.UnusedFunctionParameter)
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            Category::ENTITY,
            'manufacturer',
            [
                'type'                     => 'int',
                'label'                    => 'Manufacturer',
                'input'                    => 'select',
                'required'                 => false,
                'default'                  => null,
                'sort_order'               => 10,
                'global'                   => ScopedAttributeInterface::SCOPE_STORE,
                'group'                    => 'Content',
                'is_html_allowed_on_front' => true,
            ]
        );
        $setup->endSetup();
    }
}
