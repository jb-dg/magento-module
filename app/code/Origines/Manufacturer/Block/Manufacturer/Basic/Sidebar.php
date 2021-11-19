<?php

namespace Origines\Manufacturer\Block\Manufacturer\Basic;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;

class Sidebar extends BasicManufacturerBlock
{
    /**
     * @param $category
     *
     * @return string
     */
    public function getCategoryUrl($category)
    {
        return $this->categoryHelper->getCategoryUrl($category);
    }

    /**
     * Get child category html
     *
     * @param $category
     * @param string $html
     * @param int $level
     * @return string
     */
    public function getChildCategoryHtml($category, $html = '', $level = 1)
    {
        /** @var Category $category */
        if ($category->hasChildren()) {
            $childCategories = $category->getChildrenCategories();
            if (!empty($childCategories)) {
                $html .= '<ul class="o-list o-list--unstyled">';
                foreach ($childCategories as $childCategory) {
                    $active = $this->isActive($childCategory);
                    /** @var Category $childCategory */
                    $html .= '<li class="level' . $level . ($active ? ' active' : '') . '">';
                    $html .= '<a href="'
                        . $this->getCategoryUrl($childCategory)
                        . '" title="' . $childCategory->getName()
                        . '" class="' . ($active ? 'is-active' : '')
                        . '"><span>'
                        . $childCategory->getName()
                        . '</span></a>';

                    if ($childCategory->hasChildren()) {
                        if ($active) {
                            $html .= '<span class="expanded collapse"><i class="fa fa-minus"></i></span>';
                        } else {
                            $html .= '<span class="expand collapse"><i class="fa fa-plus"></i></span>';
                        }
                        $html .= $this->getChildCategoryHtml($childCategory, '', ($level + 1));
                    }
                    $html .= '</li>';
                }
                $html .= '</ul>';
            }
        }

        return $html;
    }

    /**
     * Is active
     *
     * @param Category $category
     * @return bool
     */
    public function isActive($category)
    {
        list($isProductPage, $isActive) = $this->isActiveOnProductPage($category);

        if ($isProductPage) {
            return $isActive;
        }

        return $this->isActiveOnCategoryPage($category);
    }

    /**
     * Is Active On Product Page
     *
     * @param $category
     * @return array
     */
    public function isActiveOnProductPage($category)
    {
        $currentProduct = $this->getCurrentProduct();

        if ($currentProduct !== null) {
            $ids = $this->getProductViewModel()->getProductPathCategoryIds($currentProduct);

            return [true, in_array($category->getId(), $ids)];
        }

        return [false, false];
    }

    /**
     * Is active on category page
     *
     * @param $category
     * @return bool
     */
    public function isActiveOnCategoryPage($category)
    {
        $currentCategory = $this->getCurrentCategory();

        if ($this->isCurrentCategory($category, $currentCategory)) {
            return true;
        }

        if ($this->isSubCategory($category, $currentCategory)) {
            return true;
        }

        if ($this->isParentCategory($category, $currentCategory)) {
            return true;
        }

        $isSameName = $category->getName() == $currentCategory->getName();
        return $isSameName;
    }

    /**
     * Is Current Category
     *
     * @param $category
     * @param $currentCategory
     * @return bool
     */
    public function isCurrentCategory($category, $currentCategory)
    {
        return $category->getId() == $currentCategory->getId();
    }

    /**
     * Is sub category
     *
     * @param $category
     * @param $currentCategory
     * @return bool
     */
    public function isSubCategory($category, $currentCategory)
    {
        $isSubCategory = false;
        $childrenIds = $category->getAllChildren(true);
        if ($childrenIds !== null) {
            $isSubCategory = in_array($currentCategory->getId(), $childrenIds);
        }

        return $isSubCategory;
    }

    /**
     * Is parent category
     *
     * @param $category
     * @param $currentCategory
     * @return bool
     */
    public function isParentCategory($category, $currentCategory)
    {
        return count($currentCategory->getPathIds())
            && in_array($category->getId(), $currentCategory->getPathIds());
    }
}
