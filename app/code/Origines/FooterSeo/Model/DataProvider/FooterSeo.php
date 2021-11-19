<?php

namespace Origines\FooterSeo\Model\DataProvider;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Origines\FooterSeo\Model\ResourceModel\FooterSeo\Collection;
use Origines\FooterSeo\Model\ResourceModel\FooterSeo\CollectionFactory;

class FooterSeo extends DataProvider
{
    /** @var array $loadedData */
    private $loadedData;

    /** @var CollectionFactory $collectionFactory */
    private $collectionFactory;

    /** @var StoreManagerInterface $storeManager */
    private $storeManager;

    /**
     * FooterSeo constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManager
     * @param array $meta
     * @param array $data
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
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $items = $collection->getItems();
        foreach ($items as $footerSeo) {
            $footerSeoData = $footerSeo->getData();
            $footerSeoData['type_id'] = explode(',', $footerSeoData['type_id']);
            $footerSeoData['store_view'] = explode(',', $footerSeoData['store_view']);
            $footerSeo->setData($footerSeoData);
            $this->loadedData[$footerSeo->getId()] = $footerSeoData;
        }
        return $this->loadedData;
    }
}
