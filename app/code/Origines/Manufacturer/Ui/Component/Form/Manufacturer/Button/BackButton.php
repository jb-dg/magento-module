<?php

namespace Origines\Manufacturer\Ui\Component\Form\Manufacturer\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton implements ButtonProviderInterface
{
    /** @var \Magento\Framework\UrlInterface $urlBuilder */
    private $urlBuilder;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->urlBuilder = $context->getUrlBuilder();
    }

    public function getButtonData()
    {
        return [
            'label'      => __('Back'),
            'on_click'   => sprintf("location.href = '%s';", $this->urlBuilder->getUrl('*/*/')),
            'class'      => 'back',
            'sort_order' => 10
        ];
    }

    public function getDeleteUrl($id)
    {
        return $this->urlBuilder->getUrl('*/*/delete', ['id' => $id]);
    }
}
