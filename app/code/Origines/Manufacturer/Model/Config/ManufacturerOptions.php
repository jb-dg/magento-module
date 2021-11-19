<?php

namespace Origines\Manufacturer\Model\Config;

use Magento\Catalog\Model\Category;
use Magento\Framework\Registry;
use Origines\Manufacturer\Service\ManufacturerService;

class ManufacturerOptions implements \Magento\Framework\Option\ArrayInterface
{
    /** @var Registry $registry */
    private $registry;

    /** @var ManufacturerService $manufacturerService */
    private $manufacturerService;

    /**
     * @param ManufacturerService $manufacturerService
     * @param Registry            $registry
     */
    public function __construct(
        ManufacturerService $manufacturerService,
        Registry $registry
    ) {
        $this->registry = $registry;
        $this->manufacturerService = $manufacturerService;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function toOptionArray()
    {
        /** @var Category $category */
        $category = $this->registry->registry('category');

        $options = $this->manufacturerService->getAvailableManufacturerCollection($category)->toOptionArray();
        $options[] = [
            'value' => null,
            'label' => __('None')
        ];

        return $options;
    }
}
