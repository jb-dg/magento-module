<?php
namespace Origines\CronImport\Model\Import;

use Magento\Framework\App\Filesystem\DirectoryList;
use Origines\CronImport\Model\Import\Source\Csv as CsvModel;
use Psr\Log\LoggerInterface;

class ImportAll
{
    const IMPORT_EXPORT_DIR = 'origines/cronimport/';
    const IMPORT_EXPORT_ARCHIVE_DIR = 'origines/cronimport/archives/';
    /**
     * @var DirectoryList
     */
    protected $_directoryList;

    /**
     * @var Driver\File
     */
    protected $_fileDriver;

    public function __construct(
        DirectoryList $directoryList,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        CsvModel $csv,
        LoggerInterface $logger
    ) {
        $this->_directoryList = $directoryList;
        $this->_fileDriver = $fileDriver;
        $this->_csv = $csv;
        $this->_logger = $logger;
    }

    /**
     * Import or delete
     *
     * @param string $file
     * @param \Origines\CronImport\Model\Import $importModel
     * @param bool $delete
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function process($file, $importModel, $delete = false)
    {
        $mediaDirectory = $this->_directoryList->getPath(DirectoryList::MEDIA);
        $filePath = $mediaDirectory.'/'.self::IMPORT_EXPORT_DIR . $file;
        if (!$this->_fileDriver->isDirectory($filePath) && $this->_fileDriver->isExists($filePath)) {
            $orderSourceCsv = $this->_csv->createSourceCsvModel($filePath);
            foreach ($orderSourceCsv as $rowNum => $rowData) {
                try {
                    $delete ? $importModel->delete($rowData) : $importModel->import($rowData);
                } catch (\Exception $e) {
                    $this->_logger->log('100', $file . '-' . $rowNum .': ' . $e->getMessage());
                }
            }
            $this->moveToArchives($file);
        }
    }

    /**
     * move files to archives
     *
     * @param string $file
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function moveToArchives($file)
    {
        $mediaDirectory = $this->_directoryList->getPath(DirectoryList::MEDIA);
        $src = $mediaDirectory.'/'.self::IMPORT_EXPORT_DIR.$file;
        if (!$this->_fileDriver->isDirectory($src)) {
            $destination = $mediaDirectory.'/'.self::IMPORT_EXPORT_ARCHIVE_DIR.date('mdY_Hmi').$file;
            $this->_fileDriver->rename($src, $destination);
        }
    }
}
