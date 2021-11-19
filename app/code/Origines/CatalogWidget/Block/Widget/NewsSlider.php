<?php
namespace Origines\CatalogWidget\Block\Widget;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\LayoutFactory;
use Magento\Rule\Model\Condition\Sql\ExpressionFactory;
use TBD\CatalogWidget\Block\Product\ProductsList;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Rule\Model\Condition\Sql\Builder;
use Magento\CatalogWidget\Model\Rule;
use Magento\Widget\Helper\Conditions;
class NewsSlider extends ProductsList
{

    protected $_storeManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Block\Product\Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        Builder $sqlBuilder,
        Rule $rule,
        Conditions $conditionsHelper,
        array $data = [],
        Json $json = null,
        LayoutFactory $layoutFactory = null,
        EncoderInterface $urlEncoder = null,
        ExpressionFactory $expressionFactory,
        PriceCurrencyInterface $priceCurrency
    )
    {
        $this->_storeManager = $storeManager;
        parent::__construct(
            $context, $productCollectionFactory, $catalogProductVisibility, $httpContext, $sqlBuilder,
            $rule, $conditionsHelper, $data , $json, $layoutFactory , $urlEncoder , $expressionFactory, $priceCurrency);
    }

    public function getMediaUrl($path)
    {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $path;
    }
}
