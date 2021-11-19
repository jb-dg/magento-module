<?php
namespace Origines\CronExport\Controller\Adminhtml\Export;

class ExportCustomersToCsv extends \Magento\Framework\App\Action\Action
{
    protected $_exportCustomersModel;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Origines\CronExport\Model\Export\ExportCustomers $exportCustomersModel
    ) {
        $this->_exportCustomersModel = $exportCustomersModel;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->_exportCustomersModel->export(true);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $resultRedirect->setPath('*/index/index');
    }
}
