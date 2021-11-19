<?php

namespace Origines\FooterSeo\Model\Import;

use Magento\ImportExport\Model\Import\Entity\AbstractEntity;
use Magento\Framework\Json\Helper\Data as JsonData;
use Magento\ImportExport\Helper\Data as ImportExportData;
use Magento\ImportExport\Model\ResourceModel\Import\Data;
use Magento\Eav\Model\Config;
use Magento\Framework\App\ResourceConnection;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;

class ExtendedAbstractEntity extends AbstractEntity
{
    const ENTITY_CODE = 'footerseo';

    /**
     * Need to log in import history
     *
     * @var bool
     */
    protected $logInHistory = true;

    /**
     * ExtendedAbstractEntity constructor.
     * @param JsonData $jsonHelper
     * @param ImportExportData $importExportData
     * @param Data $importData
     * @param Config $config
     * @param ResourceConnection $resource
     * @param Helper $resourceHelper
     * @param StringUtils $string
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     */
    public function __construct(
        JsonData $jsonHelper,
        ImportExportData $importExportData,
        Data $importData,
        Config $config,
        ResourceConnection $resource,
        Helper $resourceHelper,
        StringUtils $string,
        ProcessingErrorAggregatorInterface $errorAggregator
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->string = $string;
        $this->errorAggregator = $errorAggregator;

        foreach ($this->errorMessageTemplates as $errorCode => $message) {
            $this->getErrorAggregator()->addErrorMessageTemplate($errorCode, $message);
        }
        $this->_dataSourceModel = $importData;
        $this->_connection = $resource->getConnection();
    }

    /**
     * EAV entity type code getter.
     *
     * @abstract
     * @return string
     */
    public function getEntityTypeCode()
    {
        return self::ENTITY_CODE;
    }

    /**
     * Import data rows.
     *
     * @abstract
     */
    protected function _importData()
    {
        $this->_validatedRows = null;
    }

    /**
     * Row validation
     *
     * @param array $rowData
     * @param int $rowNum
     *
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }

        $this->_validatedRows[$rowNum] = true;
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }
}
