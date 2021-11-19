<?php

namespace Origines\Manufacturer\Model;

use Magento\Catalog\Model\Category;
use Origines\Manufacturer\Model\ResourceModel\Manufacturer\Collection;
use Origines\Manufacturer\Model\ResourceModel\Manufacturer\CollectionFactory;

class ManufacturerRepository
{
    /** @var CollectionFactory $collectionFactory */
    private $collectionFactory;

    /** @var ManufacturerFactory $manufacturerFactory */
    private $manufacturerFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
        ManufacturerFactory $manufacturerFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->manufacturerFactory = $manufacturerFactory;
    }

    /**
     * @param int $id
     *
     * @return Manufacturer|null
     */
    public function getById($id)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = $this->getCollection()->getItemById($id);
        return $manufacturer;
    }

    /**
     * @inheritdoc
     */
    public function getByName($name)
    {
        /** @var Manufacturer $manufacturer */
        $manufacturer = $this->getCollection()->getByName($name)->setPageSize(1)->getLastItem();

        if ($manufacturer->getId()) {
            return $manufacturer;
        }

        return null;
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @return Manufacturer
     */
    public function create()
    {
        return $this->manufacturerFactory->create();
    }

    /**
     * @param Manufacturer $manufacturer
     *
     * @return Manufacturer
     */
    public function save(Manufacturer $manufacturer)
    {
        try {
            $manufacturer->getResource()->save($manufacturer);
        } catch (\Exception $e) {
            return null;
        }

        return $manufacturer;
    }

    /**
     * @param Manufacturer $manufacturer
     *
     * @throws \Exception
     */
    public function delete(Manufacturer $manufacturer)
    {
        $manufacturer->getResource()->delete($manufacturer);
    }

    /**
     * @param $id
     *
     * @throws \Exception
     */
    public function deleteById($id)
    {
        $manufacturer = $this->getById($id);
        $manufacturer->getResource()->delete($manufacturer);
    }

    /**
     * @param Category $category
     *
     * @return Manufacturer|null
     */
    public function getByCategory(Category $category)
    {
        if ($category === null) {
            return null;
        }
        /** @var Manufacturer $manufacturer */
        $collection = $this->getCollection()->addFieldToFilter('category', $category->getEntityId());
        if ($collection->getSize() == 0) {
            return null;
        }
        $manufacturer = $collection->getLastItem();

        return $manufacturer;
    }

    /**
     * @param array $categoryIds
     *
     * @return array|null
     */
    public function getByCategoryIds($categoryIds)
    {
        if (empty($categoryIds)) {
             return null;
        }

        $collection = $this->getCollection()->addFieldToFilter('category', ['in' => $categoryIds]);
        if ($collection->getSize() == 0) {
            return null;
        }
        $manufacturers = [];

        foreach ($collection->getItems() as $item) {
            $manufacturers[$item->getCategory()->getId()] = $item;
        }

        return $manufacturers;
    }
}
