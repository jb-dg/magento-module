<?php
namespace Origines\CronExport\Cron;

class ExportCustomers
{
    /**
     * @var \Origines\CronExport\Model\Export\ExportCustomers
     */
    protected $_exportCustomersModel;

    /**
     * ExportCustomers constructor.
     *
     * @param \Origines\CronExport\Model\Export\ExportCustomers $exportCustomersModel
     */
    public function __construct(
        \Origines\CronExport\Model\Export\ExportCustomers $exportCustomersModel
    ) {
        $this->_exportCustomersModel = $exportCustomersModel;
    }

    public function execute()
    {
        $this->_exportCustomersModel->export();
    }
}
