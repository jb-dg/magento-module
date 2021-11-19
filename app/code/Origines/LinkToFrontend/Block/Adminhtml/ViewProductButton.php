<?php


namespace Origines\LinkToFrontend\Block\Adminhtml;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product;
use Magento\Store\Model\App\Emulation;
use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;

class ViewProductButton extends Container
{
    /**
     * @var Product
     */
    protected $product;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * App Emulator
     *
     * @var Emulation
     */
    protected $emulation;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * ViewProductButton constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Product $product
     * @param Emulation $emulation
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Product $product,
        Emulation $emulation,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->product = $product;
        $this->request = $context->getRequest();
        $this->emulation = $emulation;
        parent::__construct($context, $data);
    }

    /**
     * Block constructor adds buttons
     *
     */
    protected function _construct()
    {
        $this->addButton(
            'preview_product',
            $this->getButtonData()
        );
        parent::_construct();
    }

    /**
     * Return button attributes array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Customer View'),
            'on_click' => sprintf("window.open('%s')", $this->_getProductUrl()),
            'class' => 'view reset',
            'sort_order' => 20
        ];
    }

    /**
     * Get current product ID
     * @return integer | null
     */
    public function getProductId()
    {
        $product = $this->_coreRegistry->registry('product');
        if (empty($product)) {
            return null;
        }

        return $product->getId();
    }

    /**
     * Return product frontend url depends on active store
     *
     * @return mixed
     */
    protected function _getProductUrl()
    {
        $this->emulation->startEnvironmentEmulation(null, Area::AREA_FRONTEND, true);

        $store = $this->request->getParam('store');
        $product = $this->product->loadByAttribute('entity_id', $this->getProductId());

        if (empty($product)) {
            return null;
        }

        $productUrl = $product->getProductUrl();

        if ($store) {
            $productUrl = $product->setStoreId($store)->getUrlInStore();
        }

        $this->emulation->stopEnvironmentEmulation();

        return $productUrl;
    }
}
