<?php
namespace Origines\CatalogWidget\Block\Widget;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\UrlInterface;

class PushProduct extends Template implements BlockInterface
{

    protected $_storeManager;
    protected $_scopeConfig;

    public function __construct(
        StoreManagerInterface $storeManager,
        \Magento\Catalog\Block\Product\Context $context,
        ScopeConfigInterface $scopeConfig,
        Template\Context $templateContext,
        array $data = []
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        parent::__construct( $templateContext, $data);
    }

    public function getMediaUrl($path)
    {
        return $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $path;
    }
}
