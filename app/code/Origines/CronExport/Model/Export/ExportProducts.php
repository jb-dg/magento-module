<?php
namespace Origines\CronExport\Model\Export;

class ExportProducts extends AbstractEntity
{
    const PATH_SAVE_CSV_FILE = "origines/CSV/Products";
    const FILE_NAME = "Products.csv";
    const PATH_SAVE_CSV_FILE_RAZ = "origines/CSV/Raz";
    const FILE_NAME_RAZ = "Products_Raz.csv";

    /**
     * @var ProductCollectionFactory
     */
    protected $_productsCollectionFactory;

    /**
     * @var StockRegistryInterface
     */
    protected $_stockRegistry;

    /**
     * ExportProducts constructor.
     *
     * @param \Origines\CronExport\Model\Export\Adapter\Csv                  $exportCsvAdapter
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsCollection
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface           $stockRegistry
     */
    public function __construct(
        Adapter\Csv $exportCsvAdapter,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productsCollection,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    ) {
        $this->_productsCollectionFactory = $productsCollection;
        $this->_stockRegistry = $stockRegistry;
        parent::__construct($exportCsvAdapter);
    }

    public function getProductsCollectionFactoryToRaz()
    {
        return $collection = $this->_productsCollectionFactory->create()
            ->addMediaGalleryData()
            ->load();
    }

    public function getProductsCollectionFactory()
    {
        return $collection = $this->_productsCollectionFactory->create()
            ->addFieldToSelect('*')
            ->load();
    }

    public function getStockByProductId($productId)
    {
        return $this->_stockRegistry->getStockItem($productId)->getQty();
    }

    /**
     * Get ID and SKU Products Collection and prepapre data.
     * @return array
     */
    public function prepareDataToExportFull()
    {
        $collection = $this->getProductsCollectionFactory();
        $data = [];
        foreach ($collection as $key => $item) {
            $productId = $item->getEntityId();
            $item = [
                'entity_id' => $productId,
                'sku'=> $item->getSku(),
                'ean'=> (!empty($item->getEan())) ? $item->getEan() : 'no EAN',
                'stock_quantity' => $this->getStockByProductId($productId),
                'name'=> str_replace(["\r\n","\n","\t"], "", $item->getName()),
                'price'=> (!empty($item->getPrice())) ? $item->getPrice() : 0.00,
                'special_price'=> (!empty($item->getSpecialPrice())) ? $item->getSpecialPrice() : 0.00,
                'url_key'=>  $item->getUrlKey(),
                'meta_title'=> str_replace(["\r\n","\n","\t"], "", $item->getMetaTitle()),
                'meta_description'=> str_replace(["\r\n","\n","\t"], "", $item->getMetaDescription())
            ];
            $data[] =  str_replace(["\r\n","\n","\t","|"], "", $item);
        }
        return $data;
    }

    /**
     * Get ID and SKU Products Collection and prepapre data.
     * @return array
     */
    public function prepareDataToExportRaz()
    {
        $collection = $this->getProductsCollectionFactoryToRaz();
        $data = [];
        foreach ($collection as $key => $item) {
            $productId = $item->getEntityId();
            $mediaGalleryImage = $item->getMediaGalleryImages()->getFirstItem();
            $productImagePath = ($mediaGalleryImage ? $mediaGalleryImage->getFile() : '');

            $item = [
                'entity_id' => $productId,
                'sku'=> $item->getSku(),
                'image' => $productImagePath,
            ];
            $data[] =  str_replace(["\r\n","\n","\t","|"], "", $item);
        }

        return $data;
    }

    public function export($download = false, $delimiter = "|")
    {
        $data = $this->prepareDataToExportFull();
        $path = self::PATH_SAVE_CSV_FILE;
        $fileName = self::FILE_NAME;
        return $this->exportCsvAdapter->export($data, $path, $fileName, $delimiter, $download);
    }

    public function exportRaz($download = false, $delimiter = "|")
    {
        $data = $this->prepareDataToExportRaz();
        $path = self::PATH_SAVE_CSV_FILE_RAZ;
        $fileName = self::FILE_NAME_RAZ;
        return $this->exportCsvAdapter->export($data, $path, $fileName, $delimiter, $download);
    }

}
