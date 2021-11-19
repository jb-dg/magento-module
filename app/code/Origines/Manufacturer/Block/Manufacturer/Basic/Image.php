<?php

namespace Origines\Manufacturer\Block\Manufacturer\Basic;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Origines\Manufacturer\Service\ManufacturerImageService;
use Sutunam\Catalog\ViewModel\Product as ProductViewModel;

class Image extends BasicManufacturerBlock
{
    private  $manufacturerImageService;
    protected  $productViewModel;

    /**
     * Image constructor.
     * @param Context $context
     * @param Registry $registry
     * @param CategoryHelper $categoryHelper
     * @param ManufacturerImageService $manufacturerImageService
     * @param ProductViewModel $productViewModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = [],
        Registry $registry,
        CategoryHelper $categoryHelper,
        ManufacturerImageService $manufacturerImageService,
        ProductViewModel $productViewModel
    ) {
        $this->manufacturerImageService = $manufacturerImageService;
        parent::__construct($context, $data, $registry, $categoryHelper, $productViewModel);
    }

    /**
     * Get manufacturer logo
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getManufacturerLogo()
    {
        $result = "";
        $manufacturer = $this->getManufacturer();
        if ($manufacturer) {
            $result = $this->manufacturerImageService->getImageUrl($manufacturer->getImage());
        }
        return $result;
    }

    /**
     * Get manufacturer category url
     *
     * @return string
     */
    public function getManufacturerCategoryUrl()
    {
        $category = $this->getManufacturerMainCategory();
        return $this->categoryHelper->getCategoryUrl($category);
    }

    /**
     * check if Full Action Name == catalog_category_view
     *
     * @return boolean
     */
    public function isCategoryView()
    {
        return ($this->getRequest()->getFullActionName() == 'catalog_category_view') ? true : false;
    }

    /**
     * Get category logo
     *
     * @return array
     */
    public function getCategoryLogo(): array
    {
        if ($this->isCategoryView()) {
            $tagsContainerTitle = "<h1 class='container-title'>";
            $endTags = "</h1>";
            $brandName = $this->getProductViewModel()->getCurrentCategory()->getName();
        } else {
            $product = $this->getProductViewModel()->getCurrentProduct();
            $brandName = $product->getAttributeText("manufacturer");
            $tagsContainerTitle = "<h3 class='container-title'>";
            $endTags = "</h3>";
        }

        return [$tagsContainerTitle, $brandName, $endTags];
    }
}
