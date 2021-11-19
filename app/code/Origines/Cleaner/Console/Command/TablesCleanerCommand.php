<?php

namespace Origines\Cleaner\Console\Command;

use Origines\Cleaner\Helper\Config as CleanerHelperConfig;
use Origines\Cleaner\Model\CleanerManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TablesCleanerCommand extends Command
{
    const TABLES_NAME = 'tables';
    const SAVE_DAYS = 'days';
    const FORCE = 'force';

    /**
     * @var CleanerManager
     */
    protected $cleanerManager;

    /**
     * @var CleanerHelperConfig
     */
    protected $cleanerHelperConfig;

    public function __construct(
        CleanerManager $cleanerManager,
        CleanerHelperConfig $cleanerHelperConfig,
        string $name = null
    )
    {
        $this->cleanerManager = $cleanerManager;
        $this->cleanerHelperConfig = $cleanerHelperConfig;
        parent::__construct($name);
    }

    public function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        if(!$this->cleanerHelperConfig->isEnabled()) {
            $output->writeln('<error>'.__('Cleaner Table is disabled. See BO > Stores > Config > Advanced > System > Cleaner Setting'.'</error>'));
            die();
        }
    }

    /**
     * {@inheritDoc}
     * @description php bin/magento origines:cleaner:table --tables mst_search_report_log,report_viewed_product_index --days 30
     */
    protected function configure()
    {
        $this->setDescription('This is cleaner BDD Table command.');
        $this->addOption(
            self::TABLES_NAME,
            null,
            InputOption::VALUE_REQUIRED,
            'Tables name to clean'
        );

        $cleanerDaysLogs = $this->cleanerHelperConfig->getCleanerDaysLogs();
        $this->addOption(
            self::SAVE_DAYS,
            null,
            InputOption::VALUE_OPTIONAL,
            'Save X days to clean. Default : ' . $cleanerDaysLogs . 'days',
            $cleanerDaysLogs
        );

        $this->addOption(
            self::FORCE,
            'f',
            InputOption::VALUE_NONE,
            'Force table execution'
        );

        parent::configure();
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tablesName = $input->getOption(self::TABLES_NAME);
        if (!$tablesName) {
            $output->writeln('<error>' . self::TABLES_NAME . ' can not be null</error>');
            die();
        }
        $tablesName = explode(',', $tablesName);
        foreach ($tablesName as $table) {
            try {
                if($input->getOption(self::FORCE)){
                    $this->cleanerManager->addTableAvailable($table);
                }
                $saveDays = $input->getOption(self::SAVE_DAYS);
                $this->cleanerManager->cleanTable($table, $saveDays);
                $output->writeln('<info>Clean table '.$table.' for '. $saveDays .'Days.</info>');
            } catch (\Exception $e) {
                $output->writeln('<error>'.$e->getMessage().'</error>');
            }
        }
    }
}
