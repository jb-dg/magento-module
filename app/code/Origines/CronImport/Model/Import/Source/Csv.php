<?php
namespace Origines\CronImport\Model\Import\Source;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Csv create new CSV file and add Error data in additional column
 */
class Csv
{
    /**
     * @var \Magento\ImportExport\Model\Import\Source\CsvFactory
     */
    public $sourceCsvFactory;

    /**
     * @var \Magento\ImportExport\Model\Export\Adapter\CsvFactory
     */
    public $outputCsvFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    public $filesystem;

    public function __construct(
        \Magento\ImportExport\Model\Import\Source\CsvFactory $sourceCsvFactory,
        \Magento\ImportExport\Model\Export\Adapter\CsvFactory $outputCsvFactory,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->sourceCsvFactory = $sourceCsvFactory;
        $this->outputCsvFactory = $outputCsvFactory;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $sourceFile
     * @return \Magento\ImportExport\Model\Import\Source\Csv
     */
    public function createSourceCsvModel($sourceFile)
    {
        $obj = $this->sourceCsvFactory->create(
            [
                'file' => $sourceFile,
                'directory' => $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)
            ]
        );
        return $obj;
    }

    /**
     * @param string $destination
     * @return \Magento\ImportExport\Model\Export\Adapter\Csv
     */
    public function createOutputCsvModel($destination)
    {
        return $this->outputCsvFactory->create(
            [
                'destination' => $destination
            ]
        );
    }
}
