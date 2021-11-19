<?php

namespace Origines\CatalogWidget\Plugin\Block\Product;

use Sutunam\Catalog\ViewModel\Product as ProductViewModel;

class ListProduct
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

        $subject->addData([
            'product_view_model'=>$this->getProductViewModel()]);
        return $subject;
    }
}
