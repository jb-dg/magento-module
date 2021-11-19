<?php

namespace Origines\Manufacturer\Model\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Origines\Manufacturer\Model\ResourceModel\Manufacturer\Collection;
use Origines\Manufacturer\Model\ResourceModel\Manufacturer\CollectionFactory;
use Origines\Manufacturer\Service\ManufacturerImageService;

class Manufacturer extends DataProvider
{
    /** @var array $loadedData */
    private $loadedData;

    /** @var CollectionFactory $collectionFactory */
    private $collectionFactory;

    /** @var StoreManagerInterface $storeManager */
    private $storeManager;

    /** @var ManufacturerImageService $manufacturerImageService */
    private $manufacturerImageService;

    /**
     * @param string                   $name
     * @param string                   $primaryFieldName
     * @param string                   $requestFieldName
     * @param ReportingInterface       $reporting
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param RequestInterface         $request
     * @param FilterBuilder            $filterBuilder
     * @param CollectionFactory        $collectionFactory
     * @param StoreManagerInterface    $storeManager
     * @param ManufacturerImageService $manufacturerImageService
     * @param array                    $meta
     * @param array                    $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        CollectionFactory $collectionFactory,
        StoreManagerInterface $storeManager,
        ManufacturerImageService $manufacturerImageService,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->manufacturerImageService = $manufacturerImageService;
    }

    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $items = $collection->getItems();
        foreach ($items as $manufacturer) {
            /** @var \Origines\Manufacturer\Model\Manufacturer $manufacturer */
            $this->loadedData[$manufacturer->getId()] = $manufacturer->getData();
            if (!empty($this->loadedData[$manufacturer->getId()]['image'])) {
                $imageName = $this->loadedData[$manufacturer->getId()]['image'];
                unset($this->loadedData[$manufacturer->getId()]['image']);
                $this->loadedData[$manufacturer->getId()]['image'][0]['name'] = $imageName;

                $imageUrl = $this->manufacturerImageService->getImageUrl($imageName);
                $this->loadedData[$manufacturer->getId()]['image'][0]['url'] = $imageUrl;
            }
        }

        return $this->loadedData;
    }
}
