<?php
namespace Origines\Config\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeData implements UpgradeDataInterface
{
	/** const to Update visibility for states */
	const STATES_TO_UPDATE = ['complete', 'closed', 'processing', 'hipay_partially_refunded'];
	const TABLE_SALES_ORDER_STATUS_STATE = 'sales_order_status_state';

	public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		if (version_compare($context->getVersion(), '1.0.5', '<')) {

			/** Update visibility for states */
			$setup->getConnection()->update(
		        $setup->getTable(self::TABLE_SALES_ORDER_STATUS_STATE),
		        ['visible_on_front' => 0],
		        ['state NOT IN (?)' => self::STATES_TO_UPDATE]
		    );
		}
	}
}
