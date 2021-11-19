<?php

namespace Origines\Cleaner\Cron;

use Mirasvit\SearchReport\Api\Data\LogInterface;
use Origines\Cleaner\Helper\Config as CleanerHelperConfig;
use Origines\Cleaner\Model\CleanerManager;
use Psr\Log\LoggerInterface;

class TablesCleaner
{
    /**
     * @var CleanerManager
     */
    protected $cleanerManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var CleanerHelperConfig
     */
    protected $cleanerHelperConfig;

    /**
     * TablesCleaner constructor.
     * @param CleanerManager $cleanerManager
     * @param LoggerInterface $logger
     * @param CleanerHelperConfig $cleanerHelperConfig
     */
    public function __construct(
        CleanerManager $cleanerManager,
        LoggerInterface $logger,
        CleanerHelperConfig $cleanerHelperConfig
    )
    {
        $this->cleanerManager = $cleanerManager;
        $this->logger = $logger;
        $this->cleanerHelperConfig = $cleanerHelperConfig;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute()
    {
        if($this->cleanerHelperConfig->isEnabled()) {
            $tables = $this->cleanerManager->getTablesAvailable();
            foreach ($tables as $table) {
                try {
                    $this->cleanerManager->cleanTable($table, $this->cleanerHelperConfig->getCleanerDaysLogs());
                } catch (\Exception $e) {
                    $this->logger->error('CRON tables_cleaner Error : ' . $e->getMessage());
                }
            }
        }
    }
}
