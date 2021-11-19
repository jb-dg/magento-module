<?php

namespace Origines\FooterSeo\Block\Html;

use Origines\FooterSeo\Model\FooterSeoFactory;
use Magento\Framework\View\Element\Template\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Registry;
use Magento\Framework\App\Request\Http;

class FooterSeo extends \Magento\Framework\View\Element\Template
{
    const PRODUCT_VIEW = 'catalog_product_view';
    const CATEGORY_VIEW = 'catalog_category_view';

    /**
     * @var FooterSeoFactory
     */
    protected $_footerSeoFactory;

    /**
     * @var Http
     */
    protected $_request;

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * FooterSeo constructor.
     * @param Context $context
     * @param FooterSeoFactory $footerSeoFactory
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param Http $request
     * @param array $data
     */
    public function __construct(
        Context $context,
        FooterSeoFactory $footerSeoFactory,
        StoreManagerInterface $storeManager,
        Registry $registry,
        Http $request,
        array $data = []
    ) {
        $this->_footerSeoFactory = $footerSeoFactory;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->_registry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Get footer collection
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getFooterCollection()
    {
        return $footerCollection = $this->_footerSeoFactory->create()->getCollection()
            ->addFieldToSelect('footer_content_text')
            ->addFieldToFilter('status', ['eq' => 1])
            ->addFieldToFilter('store_view', ['eq' => $this->getStoreId()]);
    }

    /**
     * Get footer by view type
     *
     * @param $type
     * @param $typeId
     * @return array
     */
    public function getFooterByViewType($type, $typeId)
    {
        return $this->getFooterCollection()
            ->addFieldToFilter('type', ['eq' => $type])
            ->addFieldToFilter('type_id', ['eq' => $typeId])
            ->getData();
    }

    /**
     * Get current category
     *
     * @return mixed
     */
    public function getCurrentCategory()
    {
        return $this->_registry->registry('current_category');
    }

    /**
     * Get current product
     *
     * @return mixed
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    /**
     * Get full action name
     *
     * @return string
     */
    public function getFullActionName()
    {
        return $this->_request->getFullActionName();
    }

    /**
     * Get custom footer
     *
     * @return mixed|string
     */
    public function getCustomFooter()
    {
        switch ($this->getFullActionName()) {
            case self::PRODUCT_VIEW:
                $type = self::PRODUCT_VIEW;
                $typeId = $this->getCurrentProduct()->getSku();
                break;
            case self::CATEGORY_VIEW:
                $type = self::CATEGORY_VIEW;
                $typeId = $this->getCurrentCategory()->getId();
                break;
            default:
                return 'default';
        }

        $footer = $this->getFooterByViewType($type, $typeId);
        if (isset($footer[0]['footer_content_text']) && !empty($footer)) {
            return $this->formattedText($footer[0]['footer_content_text']);
        }

        return 'default';
    }

    /**
     * Formatted text
     */
    public function formattedText($text)
    {
        if (!empty($text)) {
            $text = str_replace(["<h1>", "<h2>", "<h4>", "<h5>", "<h6>"], "<h3>", $text);
            $text = str_replace(["</h1>", "</h2>", "</h4>", "</h5>", "</h6>"], "</h3>", $text);
        }
        return $text;
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        if (isset($storeId) && !empty($storeId)) {
            return $storeId;
        }
        return 1;
    }
}
