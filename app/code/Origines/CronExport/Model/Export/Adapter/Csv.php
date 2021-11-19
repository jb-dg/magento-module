<?php
namespace Origines\CronExport\Model\Export\Adapter;

use Magento\Framework\App\Filesystem\DirectoryList;

class Csv
{
    const PATH_ARCHIVES_FILE = "/archives/";

    protected $_fileFactory;
    protected $_csvProcessor;
    protected $_directoryList;
    protected $_fileDriver;
    protected $_serializerJson;

    public function __construct(
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Magento\Framework\Serialize\Serializer\Json $serializerJson,
        DirectoryList $directoryList
    ) {
        $this->_fileFactory = $fileFactory;
        $this->_csvProcessor = $csvProcessor;
        $this->_directoryList = $directoryList;
        $this->_fileDriver = $fileDriver;
        $this->_serializerJson = $serializerJson;
    }

    public function export($data, $path, $fileName, $delimiter = '|', $download = false)
    {
        $mediaDirectory = $this->_directoryList->getPath(DirectoryList::MEDIA);
        $filePath = $mediaDirectory . '/' . $path;
        $archiveFilePath = $mediaDirectory. '/' . $path . self::PATH_ARCHIVES_FILE;

        if (!$this->_fileDriver->isDirectory($filePath)) {
            $this->_fileDriver->createDirectory($filePath, 0755);
        }

        $filePath = $filePath . '/' . $fileName;

        (isset($data[0])) ?  $columnsCsv = array_keys($data[0]) : $columnsCsv = [];
        array_unshift($data, $columnsCsv);
        $this->_csvProcessor
            ->setEnclosure('"')
            ->setDelimiter($delimiter)
            ->saveData($filePath, $data);

        if (!$this->_fileDriver->isDirectory($archiveFilePath)) {
            $this->_fileDriver->createDirectory($archiveFilePath, 0755);
        }

        $archiveFilePath = $archiveFilePath . date('dmYHis') . '-' . $fileName;
        $this->_fileDriver->copy($filePath, $archiveFilePath);

        $filePathCsv = $path. '/' . $fileName;
        return $download ? $this->downloadCsv($filePath, $fileName) : $filePathCsv;
    }

    public function downloadCsv($filePath, $fileName)
    {
        return $this->_fileFactory->create(
            $fileName,
            [
                'type' => "filename",
                'value' => $filePath,
                'rm' => false,
            ],
            DirectoryList::MEDIA
        );
    }

    /**
     * @param $data
     * @return string
     */
    public function jsonEncode($data)
    {
         return $this->_serializerJson->serialize($data);
    }

    /**
     * @param $data
     * @return array|bool|float|int|mixed|string|null
     */
    public function jsonDecode($data)
    {
        return $this->_serializerJson->unserialize($data);
    }
}
