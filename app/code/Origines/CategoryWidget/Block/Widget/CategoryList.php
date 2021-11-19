<?php
namespace Origines\CategoryWidget\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\App\Filesystem\DirectoryList as AppDirectoryList;
use Magento\Framework\UrlInterface;
 
class CategoryList extends Template implements BlockInterface
{
    const PAGE_SIZE = 5;

    const DS = '/';

    const CATALOG_CATEGORY_PATH = 'catalog/category';

    protected $eavConfig;

    protected $categoryCollectionFactory;

    private $ressource;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $ressource,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $category,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    ) {
        $this->ressource = $ressource;
        $this->storeManager = $context->getStoreManager();
        $this->urlBuilder = $context->getUrlBuilder();
        $this->category = $category;
        $this->categoryRepository = $categoryRepository;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->conditionsHelper = $conditionsHelper;
        $this->eavConfig = $eavConfig;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * @param array $categoryIds
     * @return \Magento\Framework\Data\Collection
     */
    public function getCategory()
    {
        $categoryIds = $this->getCategoryIds();
        if (!empty($categoryIds)) {
            $ids = array_slice(explode(',', $categoryIds), 0, $this->getCategoryToDisplay());
            $category = [];
            foreach ($ids as $id) {
                $category[] = $this->categoryRepository->get($id);
            }
            return $category;
        }
        return '';
    }

    /**
     * Retrieve how many category should be displayed
     *
     * @return int
     */
    public function getCategoryToDisplay()
    {
        if (!$this->hasData('page_size')) {
            $this->setData('page_size', self::PAGE_SIZE);
        }
        return $this->getData('page_size');
    }

    /**
     * Retrieve category ids from widget
     *
     * @return string
     */
    public function getCategoryIds()
    {
        $conditions = $this->getData('conditions')
            ? $this->getData('conditions')
            : $this->getData('conditions_encoded');

        if ($conditions) {
            $conditions = $this->conditionsHelper->decode($conditions);
        }

        foreach ($conditions as $key => $condition) {
            if (!empty($condition['attribute']) && $condition['attribute'] == 'category_ids') {
                return $condition['value'];
            }
        }
        return '';
    }

    /**
     * Get base URL or real path of a image.
     *
     * @param $imgName
     * @param string $path
     * @param bool $isRealPath
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */

    public function getMediaUrl($imgName, $path = self::CATALOG_CATEGORY_PATH, $isRealPath = false)
    {
        if (!$isRealPath) {
            return $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]).$path.self::DS.$imgName;
        }

        return $this->dir->getPath(AppDirectoryList::MEDIA) . self::DS . $path . self::DS . $imgName;
    }

    public function getBrandsCategory($limit = null, $order = 'position')
    {
        $brandsCatId = 5;
        $rootCategory = 2;

        $collection = $this->categoryCollectionFactory->create();
        
        $collection->addAttributeToSelect(['name', 'path', 'url_key', 'position', 'level', 'include_in_menu'])
            ->addFieldToFilter('path', ['like' => "1/{$rootCategory}/{$brandsCatId}/%"])
            ->addLevelFilter(3)
            ->addIsActiveFilter()
            ->setPageSize($limit)
            ->setCurPage(1)
            ->addOrderField('level')
            ->addOrderField($order);

        return $collection;
    }

    public function getManufacturerListing()
    {
        $storeId = $this->storeManager->getStore()->getStoreId();
        $attributeCode = "manufacturer";
        $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode)->getAttributeId();

        $connection = $this->ressource->getConnection();
        $joinConditions = 'o.option_id = v.option_id';
        $select = $connection->select()
            ->from(['o' => 'eav_attribute_option'])
            ->join(
                ['v' => 'eav_attribute_option_value'],
                $joinConditions
            )
            ->where('o.attribute_id = ?', $attribute)
            ->where('v.store_id = ?', $storeId);

        $_category = $connection->fetchAssoc($select);
        $category = [];

        foreach ($_category as $item) {
            $category[$item['category_brand']] = [
                'id' => $item['category_brand'],
                'name' => $item['value']
            ];
        }
        return $category;
    }
}
