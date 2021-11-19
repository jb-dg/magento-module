<?php

namespace Origines\Manufacturer\Service;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Origines\Manufacturer\Model\Manufacturer;
use Origines\Manufacturer\Model\ManufacturerRepository;
use Origines\Manufacturer\Model\ResourceModel\Manufacturer\Collection as ManufacturerCollection;
use Origines\Manufacturer\Model\ResourceModel\Manufacturer\CollectionFactory as ManufacturerCollectionFactory;

class ManufacturerService
{
    /** @var ManufacturerRepository $manufacturerRepository */
    private $manufacturerRepository;

    /** @var ManufacturerCollection $manufacturerCollectionFactory */
    private $manufacturerCollectionFactory;

    public function __construct(
        ManufacturerRepository $manufacturerRepository,
        ManufacturerCollectionFactory $manufacturerCollectionFactory
    ) {
        $this->manufacturerRepository = $manufacturerRepository;
        $this->manufacturerCollectionFactory = $manufacturerCollectionFactory;
    }

    /**
     * @param Product $product
     *
     * @return bool
     * @throws \Exception
     */
    public function productHasManufacturer(Product $product)
    {
        $manufacturers = $this->getManufacturerCollectionByProduct($product);

        // For now, we'll assume there is only one manufacturer.
        // Because the today's spec are specifying that there is only one manufacturer for one product
        $manufacturer = $manufacturers->getLastItem();
        return $manufacturer !== null;
    }

    /**
     * @param Category $category
     *
     * @return bool
     */
    public function categoryHasManufacturer(Category $category)
    {
        $manufacturer = $this->getManufacturerByCategoryRecursively($category);
        return $manufacturer !== null;
    }

    /**
     * @param Product $product
     *
     * @return ManufacturerCollection
     * @throws \Exception
     */
    public function getManufacturerCollectionByProduct(Product $product)
    {
        /** @var ManufacturerCollection $manufacturerCollection */
        $manufacturerCollection = $this->manufacturerCollectionFactory->create();
        $manufacturerCollection->load()->removeAllItems();

        $categories = $product->getCategoryCollection();
        foreach ($categories as $category) {
            $manufacturer = $this->getManufacturerByCategoryRecursively($category);
            if ($manufacturer !== null && $manufacturerCollection->getItemById($manufacturer->getId()) === null) {
                $manufacturerCollection->addItem($manufacturer);
            }
        }

        return $manufacturerCollection;
    }

    /**
     * @param Category $category
     *
     * @return null|Manufacturer
     */
    public function getManufacturerByCategory(Category $category)
    {
        return $this->manufacturerRepository->getByCategory($category);
    }

    public function getManufacturerById($id)
    {
        return $this->manufacturerRepository->getById($id);
    }

    /**
     * @param Category|null $category
     *
     * @return ManufacturerCollection
     * @throws \Exception
     */
    public function getAvailableManufacturerCollection(Category $category = null)
    {
        /** @var ManufacturerCollection $available */
        $available = $this->manufacturerCollectionFactory->create()->addFieldToFilter('category', ['null' => true]);
        if ($category !== null) {
            $current = $this->getManufacturerByCategory($category);
            if ($current !== null) {
                $available->addItem($current);
            }
        }

        return $available;
    }

    /**
     * @param Manufacturer $manufacturer
     * @param Category     $category
     */
    public function setManufacturerCategory(Manufacturer $manufacturer, $category)
    {
        $manufacturer->setCategory($category);
        $this->manufacturerRepository->save($manufacturer);
    }

    /**
     * @param Category $category
     *
     * @return null|Manufacturer
     */
    public function getManufacturerByCategoryRecursively(Category $category)
    {
        $pathIds = array_reverse($category->getPathIds());
        $manufacturers = $this->manufacturerRepository->getByCategoryIds($pathIds);

        if ($manufacturers) {
            foreach ($pathIds as $id) {
                if (isset($manufacturers[ $id ])) {
                    return $manufacturers[ $id ];
                }
            }
        }

        return null;
    }
}
