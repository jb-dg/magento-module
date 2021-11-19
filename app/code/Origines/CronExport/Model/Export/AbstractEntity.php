<?php

namespace Origines\CronExport\Model\Export;

abstract class AbstractEntity
{
    protected $exportCsvAdapter;

    public function __construct(
        Adapter\Csv $exportCsvAdapter
    ) {
        $this->exportCsvAdapter = $exportCsvAdapter;
    }

    abstract public function export($download = false, $delimiter = '|');
}
