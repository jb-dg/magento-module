<?php
namespace Origines\CronImport\Block\Adminhtml\Index;

class Index extends \Magento\Framework\View\Element\Template
{
    protected $formKey;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->formKey = $context->getFormKey();
        parent::__construct($context, $data);
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
