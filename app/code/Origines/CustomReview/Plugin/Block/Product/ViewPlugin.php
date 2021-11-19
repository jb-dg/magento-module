<?php
namespace Origines\CustomReview\Plugin\Block\Product;

use Magento\Catalog\Block\Product\View;

/**
 * Plugin for product View Block
 */
class ViewPlugin
{
    protected $_productReviews;

    public function __construct(
        \Origines\CustomReview\ViewModel\ProductReviews $productReviews
    ){
        $this->_productReviews = $productReviews;
    }

    /**
     * @method beforeToHtml
     * @param \Magento\Catalog\Block\Product\View $viewBlock
     *
     * @return array
     */
    public function beforeToHtml(
        View $viewBlock
    ) {
        /**
         * @var \Magento\Framework\View\Layout $layout
        */
        $layout = $viewBlock->getLayout();

        if ($this->_productReviews->isEnableApiTrustpilot() && $layout->getBlock('reviews.tab')) {
            $widgetReviewsTrustpilot = $this->getWidgetProductReviewsTrustpilot();
            $widgetReviewsTrustpilot = $widgetReviewsTrustpilot ?? '';

            $block = $layout
                ->getBlock('reviews.tab')
                ->setData('widget-product-reviews-trustpilot', $widgetReviewsTrustpilot)
                ->setTemplate('Origines_CustomReview::widget-trustbox.phtml');
        }
    }

    public function getWidgetProductReviewsTrustpilot()
    {
        return $this->_productReviews->getWidgetProductReviews();
    }
}