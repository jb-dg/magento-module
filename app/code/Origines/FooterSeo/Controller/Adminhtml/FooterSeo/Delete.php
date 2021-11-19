<?php

namespace Origines\FooterSeo\Controller\Adminhtml\FooterSeo;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Origines\FooterSeo\Model\FooterSeo;
use Exception;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\App\ResponseInterface;

/**
 * action delete footer
 */
class Delete extends Action
{
    /**
     * @var FooterSeo
     */
    protected $_model;

    /**
     * Delete constructor.
     * @param Context $context
     * @param FooterSeo $modelFooterSeo
     */
    public function __construct(
        Context $context,
        FooterSeo $modelFooterSeo
    ) {
        parent::__construct($context);
        $this->_model = $modelFooterSeo;
    }

    /**
     * Is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Origines_FooterSeo::footerseo_delete');
    }

    /**
     * Execute
     *
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model = $this->_model;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccessMessage(__('Footer deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('Footer does not exist'));
        return $resultRedirect->setPath('*/*/');
    }
}
