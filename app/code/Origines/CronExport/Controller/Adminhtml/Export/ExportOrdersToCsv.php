<?php
namespace Origines\CronExport\Controller\Adminhtml\Export;

class ExportOrdersToCsv extends \Magento\Framework\App\Action\Action
{
    protected $_exportOrdersModel;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Origines\CronExport\Model\Export\ExportOrders $exportOrdersModel
    ) {
        $this->_exportOrdersModel = $exportOrdersModel;
        $this->_baseUrl = $context->getUrl()->getBaseUrl();
        parent::__construct($context);
    }
    
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $urlFiles = $this->_exportOrdersModel->export();
            if ($urlFiles === null) {
                $this->messageManager->addError("Pas de commandes !");
            } else {
                $urlFileOrders = $this->_baseUrl.'pub/media/'.$urlFiles['url_file_orders'];
                $urlFileProductsOrders = $this->_baseUrl.'pub/media/'.$urlFiles['url_file_products_orders'];

                $this->messageManager->addSuccess(
                    "Export Réussi ! <br>
                    <a href='".$urlFileOrders."' >".$this->_exportOrdersModel::FILE_NAME_ORDERS."</a>
                    <br><a href='".$urlFileProductsOrders."'>".
                    $this->_exportOrdersModel::FILE_NAME_PRODUCTS_ORDERS."</a>"
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $resultRedirect->setPath('*/index/index');
    }
}
