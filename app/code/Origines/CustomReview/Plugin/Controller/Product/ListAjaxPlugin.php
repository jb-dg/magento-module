<?php
namespace Origines\CustomReview\Plugin\Controller\Product;

use Magento\Review\Controller\Product\ListAjax;

class ListAjaxPlugin
{
    protected $_helperData;

    public function __construct(
        \Origines\CustomReview\ViewModel\ProductReviews $productReviews
    ){
        $this->_productReviews = $productReviews;
    }

    public function afterExecute(ListAjax $subject, $result)
    {
        if ($this->_productReviews->isEnableApiTrustpilot()) {
            $productReviewsTrustpilot = $this->getReviewsTrustpilot();

            if (isset($productReviewsTrustpilot)) {
                $block = $result->getLayout()
                    ->getBlock('product.info.product_additional_data')
                    ->setData('reviews-trustpilot', $productReviewsTrustpilot)
                    ->toHtml();
            }
        }
        return $result;
    }

    public function getReviewsTrustpilot()
    {
        return $this->_productReviews->getReviewsTrustpilot();
    }
}