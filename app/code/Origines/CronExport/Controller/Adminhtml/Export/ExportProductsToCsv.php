<?php

namespace Origines\CronExport\Controller\Adminhtml\Export;

use Magento\Framework\UrlInterface;
use Magento\Framework\App\Action\Action;
use Origines\CronExport\Model\Export\ExportProducts;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ResponseInterface;

class ExportProductsToCsv extends Action
{
    const EXPORT_TYPE_RAZ = 'RAZ';

    /**
     * @var ExportProducts
     */
    protected $_exportProductsModel;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * ExportProductsToCsv constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param ExportProducts $exportProductsModel
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        ExportProducts $exportProductsModel
    ) {
        $this->_storeManager = $storeManager;
        $this->_exportProductsModel = $exportProductsModel;
        parent::__construct($context);
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface|string
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $exportType = $this->getRequest()->getParam('export-type');
            if ($exportType == self::EXPORT_TYPE_RAZ) {
                $fileUrl = $this->_exportProductsModel->exportRaz(false);
                $mediaUrl = $this->_storeManager->getStore()
                    ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
                $fileUrlRaz = $mediaUrl . $fileUrl;
                $this->messageManager->addSuccess(
                    "<strong>RAZ réussi !</strong> <em>(télécharger le fichier CSV :
                    <a href='" . $fileUrlRaz . "' >" . $this->_exportProductsModel::FILE_NAME_RAZ . "</a> )</em>"
                );
            } else {
                return $this->_exportProductsModel->export(true);
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setPath('*/index/index');
    }
}
