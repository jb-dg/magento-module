<?php

namespace Origines\Manufacturer\Observer;

use Magento\Catalog\Model\Category;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Origines\Manufacturer\Model\Manufacturer;
use Origines\Manufacturer\Service\ManufacturerService;

class UpdateManufacturerCategory implements ObserverInterface
{
    /** @var ManufacturerService $manufacturerService */
    private $manufacturerService;

    public function __construct(ManufacturerService $manufacturerService)
    {
        $this->manufacturerService = $manufacturerService;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Category $category */
        $category = $observer->getData('category');

        $origManufacturerId = $category->getOrigData('manufacturer');
        $manufacturerId = $category->getData('manufacturer');

        if ($manufacturerId !== $origManufacturerId) {
            if ($origManufacturerId !== null) {
                /** @var Manufacturer $manufacturer */
                $origManufacturer = $this->manufacturerService->getManufacturerById($origManufacturerId);
                if ($origManufacturer !== null) {
                    $this->manufacturerService->setManufacturerCategory($origManufacturer, null);
                }
            }

            if ($manufacturerId !== null) {
                /** @var Manufacturer $manufacturer */
                $manufacturer = $this->manufacturerService->getManufacturerById($manufacturerId);
                if ($manufacturer !== null) {
                    $this->manufacturerService->setManufacturerCategory($manufacturer, $category);
                }
            }
        }
    }
}
