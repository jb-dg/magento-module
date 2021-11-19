<?php

namespace Origines\Manufacturer\Ui\Component\Listing\Column;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class ManufacturerCategory extends Column
{
    /** @var CategoryRepositoryInterface $categoryRepository */
    private $categoryRepository;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CategoryRepositoryInterface $categoryRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if ($item[$fieldName] !== null) {
                    /** @var Category $category */
                    try {
                        $category = $this->categoryRepository->get($item[$fieldName]);
                    } catch (NoSuchEntityException $e) {
                        $item[$fieldName] = '';
                    }
                    $item[$fieldName] = $category->getName();
                } else {
                    $item[$fieldName] = '';
                }
            }
        }

        return $dataSource;
    }
}
