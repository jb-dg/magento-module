<?php
namespace Origines\CustomPaymentMethod\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{
	const NAME_MODULE_TO_DELETE = 'Origines_CustomHipay';
	const TABLE_SETUP_MODULE = 'setup_module';

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		if (version_compare($context->getVersion(), '1.0.0', '<')) {
		    /** Delete module */
			$setup->getConnection()->delete(
		        $setup->getTable(self::TABLE_SETUP_MODULE),
		        ['module = ?' => self::NAME_MODULE_TO_DELETE]
		    );
		}
	}
}
