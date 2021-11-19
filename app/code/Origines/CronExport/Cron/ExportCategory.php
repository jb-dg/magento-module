<?php
namespace Origines\CronExport\Cron;

class ExportCategory
{
    /**
     * @var \Origines\CronExport\Model\Export\ExportCategory
     */
    protected $_exportCategoryModel;

    /**
     * ExportCategory constructor.
     *
     * @param \Origines\CronExport\Model\Export\ExportCategory $exportCategoryModel
     */
    public function __construct(
        \Origines\CronExport\Model\Export\ExportCategory $exportCategoryModel
    ) {
        $this->_exportCategoryModel = $exportCategoryModel;
    }

    public function execute()
    {
        $this->_exportCategoryModel->export();
    }
}
