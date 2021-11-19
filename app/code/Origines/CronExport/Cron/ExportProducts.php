<?php
namespace Origines\CronExport\Cron;

class ExportProducts
{
    /**
     * @var \Origines\CronExport\Model\Export\ExportProducts
     */
    protected $_exportProductsModel;

    /**
     * ExportProducts constructor.
     *
     * @param \Origines\CronExport\Model\Export\ExportProducts $exportProductsModel
     */
    public function __construct(
        \Origines\CronExport\Model\Export\ExportProducts $exportProductsModel
    ) {
        $this->_exportProductsModel = $exportProductsModel;
    }

    public function execute()
    {
        $this->_exportProductsModel->export();
    }
}
