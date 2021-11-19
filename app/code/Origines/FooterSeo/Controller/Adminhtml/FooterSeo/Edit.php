<?php

namespace Origines\FooterSeo\Controller\Adminhtml\FooterSeo;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ResponseInterface;

class Edit extends Action
{
    /** @var Registry $registry */
    private $registry;

    /**
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry
    ) {
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * Execute
     *
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $this->registry->register('footerseo_id', $id);

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
