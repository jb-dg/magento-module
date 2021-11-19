<?php
namespace Origines\CatalogWidget\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Framework\UrlInterface;

class MySelectionProduct extends AbstractProduct implements BlockInterface
{
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Get id product selected into widget myorigines_catalogwidget_myselectionproduct
     * @return string
     */
    public function getProductIdToMySelection()
    {
        if($this->getData('id_product')) {
            return $productId = str_replace('product/', '', $this->getData('id_product'));
        }
    }

    /**
     * Get product by ID
     * @param $id
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface|mixed
     */
    public function getProductById($id)
	{
		return $this->getProductViewModel()->getProductById($id);
	}

    /**
     * Display yes/no pastille discount percent
     * Value selected into widget myorigines_catalogwidget_myselectionproduct
     * @return bool
     */
    public function isEnablePercent()
    {
        if ($this->getData('my_selection_percent')){
            return true;
        }
        return false;
    }

    /**
     * Prepare the data product to display into $_template
     * @return array
     */
    public function prepareDataItemMySelection()
    {
        /** get product select into widget my selection */
        $productId = $this->getProductIdToMySelection();
        $item = $this->getProductById($productId);
        $display = 'none';
        /** get basic data product */
        $name =  $item->getDataUsingMethod('name') ? $item->getDataUsingMethod('name') : '';
        $secondName =  $item->getDataUsingMethod('second_name') ? $item->getDataUsingMethod('second_name') : '';
        $shortDescription =  $item->getDataUsingMethod('short_description') ? $item->getDataUsingMethod('short_description') : '';
        $brand =  $item->getAttributeText('manufacturer') ? $item->getAttributeText('manufacturer') : '';
        $productUrl = $item->getDataUsingMethod('product_url');
        $addToCartUrl = $this->getAddToCartUrl($item);
        /** displays discount percent if pastille is enabled and discount > -5% */
        $isSaleable = $item->isSaleable();
        $isEnablePercent = $this->isEnablePercent();
        if ($isEnablePercent && $isSaleable) {
            $salePercent = $this->getProductViewModel()->getDiscountAmount($item, null, 'percent');
            if ($salePercent >= 5) {
                $display = 'block';
                $productUrl = $productUrl.'#news';
            } else {
                $isEnablePercent = false;
            }
        }

        /** displays old-price only if the pastille is enabled  */
        $productPrice = $this->getProductPrice($item);
        $search = ['<span class="old-price">', '<span class="old-price sly-old-price">'];
        $replace = ['<span class="old-price" style="display:'.$display.'">',
                    '<span class="old-price sly-old-price" style="display:'.$display.'">'];
        $price = str_replace($search, $replace, $productPrice);
        $productImg = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA).$this->getData('my_selection_packshot');

        return $itemMySelection = [
            'name' => $name,
            'second_name' => $secondName,
            'brand' => $brand,
            'short_description' => $shortDescription,
            'price' => $price,
            'product_url' => $productUrl,
            'is_enable_percent' => $isEnablePercent,
            'sale_percent' => isset($salePercent) ? $salePercent.' %' : '',
            'product_img' => $productImg,
            'is_saleable' => $isSaleable,
            'add_to_cart_url' => $addToCartUrl,
        ];
    }
}
