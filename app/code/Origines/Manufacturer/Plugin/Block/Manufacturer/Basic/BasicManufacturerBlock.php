<?php

namespace Origines\Manufacturer\Plugin\Block\Manufacturer\Basic;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Origines\Manufacturer\Model\Manufacturer;
use Sutunam\Catalog\Helper\Data as SutunamCatalogHelper;

abstract class BasicManufacturerBlock extends Template
{
    private \Sutunam\Catalog\ViewModel\Product $productViewModel;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param CategoryHelper $categoryHelper
     * @param SutunamCatalogHelper $sutunamCatalogHelper
     * @param array $data
     * @param \Sutunam\Catalog\ViewModel\Product $productViewModel
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CategoryHelper $categoryHelper,
        SutunamCatalogHelper $sutunamCatalogHelper,
        array $data = [],
        \Sutunam\Catalog\ViewModel\Product $productViewModel
    ) {
        $this->categoryHelper = $categoryHelper;
        $this->registry = $registry;
        $this->sutunamCatalogHelper = $sutunamCatalogHelper;
        $this->productViewModel = $productViewModel;
        parent::__construct($context, $data);
    }

    /**
     * @return \Sutunam\Catalog\ViewModel\Product
     */
    public function getProductViewModel(): \Sutunam\Catalog\ViewModel\Product
    {
        return $this->productViewModel;
    }

    public function beforeToHtml($subject)
    {
        $subject->addData(['product_view_model' => $this->getProductViewModel()]);
    }
}
