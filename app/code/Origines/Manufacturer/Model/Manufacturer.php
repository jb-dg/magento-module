<?php

namespace Origines\Manufacturer\Model;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

/**
 *
 * @method string getName()
 * @method setName(string $name)
 * @method string getImage()
 * @method setImage(string $image)
 * @method string getLayout()
 * @method setLayout(string $layout)
 */
class Manufacturer extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'origines_manufacturer';

    /** @var CategoryRepositoryInterface $categoryRepository */
    private $categoryRepository;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->categoryRepository = $categoryRepository;
    }

    public function _construct()
    {
        $this->_init(ResourceModel\Manufacturer::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param Category|null $category
     */
    public function setCategory($category)
    {
        if ($category === null) {
            $this->setData('category', null);
        } else {
            $this->setData('category', $category->getId());
        }
    }

    /**
     * @return Category|null
     */
    public function getCategory()
    {
        try {
            /** @var Category $category */
            $category = $this->categoryRepository->get($this->getData('category'));
            return $category;
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}
