<?php

namespace Origines\CatalogWidget\Plugin\Block\Product;

use Sutunam\Catalog\ViewModel\Product as ProductViewModel;

class ProductsList
{
    /**
     * @var Product
     */
    protected $productViewModel;

    public function __construct(
        ProductViewModel $productViewModel
    ) {

        $this->productViewModel = $productViewModel;
    }

    /**
     * @return ProductViewModel
     */
    public function getProductViewModel(): ProductViewModel
    {
        return $this->productViewModel;
    }

    public function beforeToHtml($subject){
        $brandWithCategory = $this->getProductViewModel()->getBrandWithCategory();
        $subject->setProductCollection($subject->createCollection());
        $postDatas = [];
        $discountAmounts = [];
        $categoryBrandUrls = [];
        foreach ($subject->getProductCollection() as $item){
            $postDatas[$item->getId()] = $this->getProductViewModel()->getPostHelper()->getPostData(
                $subject->getAddToCartUrl($item),
                ['product' => $item->getEntityId()]
            );
            $discountAmounts[$item->getId()] = $this->getProductViewModel()->getDiscountAmount($item, null, 'percent');

            $brandId = $item->getData('manufacturer');
            if (!isset($categoryBrandUrls[$brandId])){
                $categoryBrandUrls[$brandId] = $subject->escapeUrl($this->getProductViewModel()->getCategoryUrl($brandWithCategory[$brandId]));
            }
        }

        $subject->addData([
            'product_view_model'=>$this->getProductViewModel(),
            'postDatas'=>$postDatas,
            'discountAmounts'=>$discountAmounts,
            'categoryBrandUrls'=>$categoryBrandUrls]);
        return $subject;
    }
}
