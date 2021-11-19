<?php
namespace Origines\UrlCleaner\Block\Adminhtml\UrlCleaner;

class Grid extends \Magento\Backend\Block\Widget\Grid
{
    const TABLE_NAME_COLLECTION_PRODUCT = 'catalog_product_entity';

    protected $_resourceConnection;
    protected $_widgetContainer;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Block\Widget\Container $widgetContainer,
        array $data = []
    ) {
        $this->_widgetContainer = $widgetContainer;
        $this->_resourceConnection = $resourceConnection;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _prepareLayout()
    {
        $this->_widgetContainer->removeButton('add');
        return parent::_prepareLayout();
    }

    public function getIdsProduct()
    {
        $connection = $this->_resourceConnection->getConnection();
        $select = $connection->select()->from(
            ['cpe' => self::TABLE_NAME_COLLECTION_PRODUCT],
            ['entity_id']
        );
        $idsProductsCollection = $connection->fetchAll($select);
        return $idsProductsCollection;
    }

    protected function _prepareCollection()
    {
        $idsProductsCollection = $this->getIdsProduct();
        if ($this->getCollection()) {
            $this->getCollection()
                ->addFieldToFilter('entity_id', ['nin' => $idsProductsCollection])
                ->addFieldToFilter('entity_type', ['eq' => 'product']);
        }
        return parent::_prepareCollection();
    }
}
