<?php

namespace Origines\Cleaner\Model;


use Magento\Framework\App\ResourceConnection;
use Mirasvit\SearchReport\Api\Data\LogInterface;
use Origines\Cleaner\Helper\Config as CleanerHelperConfig;

class CleanerManager
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var CleanerHelperConfig
     */
    protected $cleanerHelperConfig;

    private $tablesAvailable = array(
        'mst_search_report_log',
        'report_viewed_product_index'
    );

    public function __construct(
        ResourceConnection $resourceConnection,
        CleanerHelperConfig $cleanerHelperConfig
    )
    {
        $this->resourceConnection = $resourceConnection;
        $this->cleanerHelperConfig = $cleanerHelperConfig;
    }

    /**
     * @param string $table
     * @param int $saveDays
     * @throws \Exception
     */
    public function cleanTable($table, $saveDays = CleanerHelperConfig::DEFAULT_SAVE_DAYS)
    {
        if(!$this->cleanerHelperConfig->isEnabled()) {
            throw new \Exception(__('Cleaner Table is disabled. See BO > Stores > Config > Advanced > System > Cleaner Setting'));
        }

        if (!in_array($table, $this->tablesAvailable)) {
            throw new \Exception(__('%1 not include in available tables : %2', implode(',', $this->tablesAvailable)));
        }

        /** @var \Magento\Framework\DB\Adapter\AdapterInterface $connection */
        $connection = $this->resourceConnection->getConnection();
        if($connection->isTableExists($table)) {
            if($connection->tableColumnExists($table,'created_at')) {
                $connection->delete(
                    $connection->getTableName($table),
                    'created_at < DATE_SUB(NOW() , INTERVAL ' . $saveDays . ' DAY)'
                );
            } elseif($connection->tableColumnExists($table,'added_at')) {
                $connection->delete(
                    $connection->getTableName($table),
                    'added_at < DATE_SUB(NOW() , INTERVAL ' . $saveDays . ' DAY)'
                );
            } else {
                throw new \Exception(__('Column created_at or added_at not exist'));
            }
        } else {
            throw new \Exception(__('Table %1 not found', $table));
        }
    }

    /**
     * @param string $tableAvailable
     */
    public function addTableAvailable($tableAvailable)
    {
        $this->tablesAvailable[] = $tableAvailable;
    }

    /**
     * @return string[]
     */
    public function getTablesAvailable()
    {
        return $this->tablesAvailable;
    }
}
