<?php
namespace Origines\CronExport\Controller\Adminhtml\Export;

class ExportCategoryToCsv extends \Magento\Framework\App\Action\Action
{
    protected $_exportCategoryModel;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Origines\CronExport\Model\Export\ExportCategory $exportCategoryModel
    ) {
        $this->_exportCategoryModel = $exportCategoryModel;
        parent::__construct($context);
    }
    
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->_exportCategoryModel->export(true);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $resultRedirect->setPath('*/index/index');
    }
}
