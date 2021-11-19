<?php

namespace Origines\FooterSeo\Ui\Component\Form\FooterSeo\Button;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\UrlInterface;

class BackButton implements ButtonProviderInterface
{
    /** @var UrlInterface $urlBuilder */
    private $urlBuilder;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->urlBuilder = $context->getUrlBuilder();
    }

    /**
     * Get button data
     *
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->urlBuilder->getUrl('*/*/')),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get delete url
     *
     * @param $id
     * @return string
     */
    public function getDeleteUrl($id)
    {
        return $this->urlBuilder->getUrl('*/*/delete', ['id' => $id]);
    }
}
