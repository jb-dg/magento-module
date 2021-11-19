<?php

namespace Origines\Manufacturer\Ui\Component\Form\Manufacturer\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton implements ButtonProviderInterface
{
    /** @var \Magento\Framework\UrlInterface $urlBuilder */
    private $urlBuilder;

    /** @var Registry $registry */
    private $registry;

    /**
     * @param Context  $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        $this->urlBuilder = $context->getUrlBuilder();
        $this->registry = $registry;
    }

    public function getButtonData()
    {
        $data = [];
        $id = $this->registry->registry('manufacturer_id');
        if ($id) {
            $data = [
                'label'      => __('Delete'),
                'class'      => 'delete',
                'on_click'   => 'deleteConfirm(\''
                    . __('Are you sure you want to delete this manufacturer ?')
                    . '\', \'' . $this->getDeleteUrl($id) . '\')',
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    public function getDeleteUrl($id)
    {
        return $this->urlBuilder->getUrl('*/*/delete', ['id' => $id]);
    }
}
