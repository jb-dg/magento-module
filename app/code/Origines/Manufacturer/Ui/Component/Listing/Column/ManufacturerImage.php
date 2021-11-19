<?php

namespace Origines\Manufacturer\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Origines\Manufacturer\Service\ManufacturerImageService;

class ManufacturerImage extends Column
{
    /** @var ManufacturerImageService $manufacturerImageService */
    private $manufacturerImageService;

    /**
     * @param ContextInterface         $context
     * @param UiComponentFactory       $uiComponentFactory
     * @param ManufacturerImageService $manufacturerImageService
     * @param array                    $components
     * @param array                    $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ManufacturerImageService $manufacturerImageService,
        array $components = [],
        array $data = []
    ) {
        $this->manufacturerImageService = $manufacturerImageService;

        parent::__construct($context, $uiComponentFactory, $components, $data);
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
                $url = '';
                if ($item[$fieldName] != '') {
                    $url = $this->manufacturerImageService->getImageUrl($item[$fieldName]);
                }
                $item[$fieldName . '_src'] = $url;
                $item[$fieldName . '_orig_src'] = $url;
            }
        }

        return $dataSource;
    }
}
