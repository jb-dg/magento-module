<?php
namespace Origines\CronExport\Cron;

class ExportOrders
{
    /**
     * @var \Origines\CronExport\Model\Export\ExportProducts
     */
    protected $_exportOrdersModel;

    /**
     * ExportProducts constructor.
     *
     * @param \Origines\CronExport\Model\Export\ExportOrders $exportOrdersModel
     */
    public function __construct(
        \Origines\CronExport\Model\Export\ExportOrders $exportOrdersModel
    ) {
        $this->_exportOrdersModel = $exportOrdersModel;
    }

    public function execute()
    {
        $this->_exportOrdersModel->export();
    }
}
