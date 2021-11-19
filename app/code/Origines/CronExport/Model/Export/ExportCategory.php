<?php
namespace Origines\CronExport\Model\Export;

class ExportCategory extends AbstractEntity
{
    const PATH_SAVE_CSV_FILE = "origines/CSV/Category";
    const FILE_NAME = "Category.csv";
    const COLUMN_DATA_CATEGORY = [
        'name',
        'meta_keywords',
        'meta_description',
        'second_description',
        'url_key',
        'url_path',
    ];

    /**
     * @var CategoryCollectionFactory
     */
    protected $_categoryCollection;

    /**
     * ExportCategory constructor.
     *
     * @param \Origines\CronExport\Model\Export\Adapter\Csv                     $exportCsvAdapter
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory   $categoryCollection
     */
    public function __construct(
        Adapter\Csv $exportCsvAdapter,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection
    ) {
        $this->_categoryCollection = $categoryCollection;
        parent::__construct($exportCsvAdapter);
    }

    public function getCategoryCollectionFactory()
    {
        return $collection = $this->_categoryCollection->create()
            ->addAttributeToSelect(self::COLUMN_DATA_CATEGORY)
            ->addAttributeToFilter('level', ['gteq' => 1]);
    }

    /**
     * Get Category Collection and prepapre data.
     * @return array
     */
    public function prepareDataToExport()
    {
        $collection = $this->getCategoryCollectionFactory();
        $data = [];

        foreach ($collection as $key => $item) {
            $category = [];
            foreach (self::COLUMN_DATA_CATEGORY as $nameDataColumn) {
                $category[$nameDataColumn] =  $item->getDataUsingMethod($nameDataColumn);
            }
            $data[] = str_replace(["\r\n","\n","\t","|"], "", $category);
        }
        return $data;
    }

    public function export($download = false, $delimiter = '|')
    {
        $data = $this->prepareDataToExport();
        $path = self::PATH_SAVE_CSV_FILE;
        $fileName = self::FILE_NAME;
        return $this->exportCsvAdapter->export($data, $path, $fileName, $delimiter, $download);
    }
}
