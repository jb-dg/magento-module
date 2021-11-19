<?php

namespace Origines\Manufacturer\Observer;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Page\Config;
use Origines\Manufacturer\Model\Manufacturer;
use Origines\Manufacturer\Service\ManufacturerService;

class AddCustomLayout implements ObserverInterface
{
    /** @var Registry $registry */
    private $registry;

    /** @var Config $layoutFactory */
    private $layoutFactory;

    /** @var ManufacturerService $manufacturerService */
    private $manufacturerService;

    /**
     * @param Registry            $registry
     * @param Config              $layoutFactory
     * @param ManufacturerService $manufacturerService
     */
    public function __construct(
        Registry $registry,
        Config $layoutFactory,
        ManufacturerService $manufacturerService
    ) {
        $this->registry = $registry;
        $this->layoutFactory = $layoutFactory;
        $this->manufacturerService = $manufacturerService;
    }

    /**
     * @param Observer $observer
     *
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = null;
        $fullActionName = $observer->getData('full_action_name');
        if ($fullActionName == 'catalog_product_view') {
            /** @var Product $product */
            $product = $this->registry->registry('current_product');
            $manufacturers = $this->manufacturerService->getManufacturerCollectionByProduct($product);

            // For now, we'll assume there is only one manufacturer.
            // Because the today's spec are specifying that there is only one manufacturer for one product
            if ($manufacturers->getSize()) {
                $manufacturer = $manufacturers->getLastItem();
            }
        } elseif ($fullActionName == 'catalog_category_view') {
            /** @var Category $category */
            $category = $this->registry->registry('current_category');
            $manufacturer = $this->manufacturerService->getManufacturerByCategoryRecursively($category);
        }

        if ($manufacturer !== null) {
            $this->registry->register('manufacturer', $manufacturer);
            $layout = $manufacturer->getLayout();
            if ($layout !== null) {
                $this->layoutFactory->addBodyClass('page-layout-2columns-left');
                $this->layoutFactory->setPageLayout($layout);
            }
        }
    }
}
