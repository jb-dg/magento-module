<?php

namespace Origines\CronImport\Model;

use Magento\ImportExport\Model\AbstractModel;

abstract class Import extends AbstractModel
{
    abstract public function import($data);

    abstract public function delete($data);
}
