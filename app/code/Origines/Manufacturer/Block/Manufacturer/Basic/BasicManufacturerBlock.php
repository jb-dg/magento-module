<?php

namespace Origines\Manufacturer\Block\Manufacturer\Basic;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Sutunam\Catalog\ViewModel\Product as ProductViewModel;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Origines\Manufacturer\Model\Manufacturer;
use Sutunam\Catalog\Helper\Data as SutunamCatalogHelper;

abstract class BasicManufacturerBlock extends Template
{
    /** @var CategoryHelper $categoryHelper */
    protected $categoryHelper;

    /** @var Registry $registry */
    protected $registry;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param CategoryHelper $categoryHelper
     * @param ProductViewModel $productViewModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = [],
        Registry $registry,
        CategoryHelper $categoryHelper,
        ProductViewModel $productViewModel
    ) {
        $this->categoryHelper = $categoryHelper;
        $this->registry = $registry;
        $this->productViewModel = $productViewModel;
        parent::__construct($context, $data);
    }

    /**
     * @return ProductViewModel
     */
    public function getProductViewModel(): ProductViewModel
    {
        return $this->productViewModel;
    }

    /**
     * @return Category|null
     */
    public function getManufacturerMainCategory()
    {
        $manufacturer = $this->getManufacturer();
        $result = null;
        if ($manufacturer) $result = $manufacturer->getCategory();
        return $result;
    }

    /**
     * @return Manufacturer|null
     */
    public function getManufacturer()
    {
        return $this->registry->registry('manufacturer');
    }

    /**
     * @return Product|null
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @return Category|null
     */
    public function getCurrentCategory()
    {
        return $this->registry->registry('current_category');
    }
}
