<?php


namespace Origines\LinkToFrontend\Block\Adminhtml;

use Magento\Backend\Block\Widget\Container;
use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Category;
use Magento\Store\Model\App\Emulation;
use Magento\Framework\App\Area;
use Magento\Framework\App\RequestInterface;

class ViewCategoryButton extends Container
{
    const DEFAULT_CATEGORY_ID = 2;

    /**
     * @var Category
     */
    protected $category;

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
     * ViewCategoryButton constructor.
     * @param Context $context
     * @param Registry $registry
     * @param Category $category
     * @param Emulation $emulation
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Category $category,
        Emulation $emulation,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->category = $category;
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
        $rootCategoryId = $this->_storeManager->getStore()->getRootCategoryId();

        if (!in_array($this->getCategoryId(), [
                $rootCategoryId,
                self::DEFAULT_CATEGORY_ID
        ])) {
            $this->addButton(
                'preview_category',
                $this->getButtonData()
            );
        }

        parent::_construct();
    }

    /**
     * Return button attributes array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Customer View'),
            'on_click' => sprintf("window.open('%s')", $this->getCategoryUrl()),
            'class' => 'view reset',
            'sort_order' => 20
        ];
    }

    /**
     * Get current category ID
     * @return integer | null
     */
    public function getCategoryId()
    {
        $category = $this->_coreRegistry->registry('category');
        if (empty($category)) {
            return null;
        }

        return $category->getId();
    }

    /**
     * Return product frontend url depends on active store
     *
     * @return mixed
     */
    protected function getCategoryUrl()
    {
        $this->emulation->startEnvironmentEmulation(null, Area::AREA_FRONTEND, true);

        $category = $this->category->loadByAttribute('entity_id', $this->getCategoryId());

        if (empty($category)) {
            return null;
        }

        $categoryUrl = $category->getUrl();

        $this->emulation->stopEnvironmentEmulation();

        return $categoryUrl;
    }
}
