<?php
namespace Origines\UrlCleaner\Controller\Adminhtml\UrlCleaner;

use Magento\Framework\App\Action\HttpPostActionInterface;
use Origines\UrlCleaner\Model\ResourceModel\UrlCleanerCollectionFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;

class MassDelete extends \Magento\Backend\App\Action implements HttpPostActionInterface
{
    protected $_urlCleanerCollectionFactory;

    public function __construct(
        Context $context,
        UrlCleanerCollectionFactory $urlCleanerCollectionFactory
    ) {
        $this->_urlCleanerCollectionFactory = $urlCleanerCollectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $ids = $this->getRequest()->getParam('massaction');
        $collection = $this->_urlCleanerCollectionFactory->create()->addFieldToFilter('url_rewrite_id', ['in' => $ids]);
        $collectionSize = $collection->getSize();
        $collection->walk('delete');

        $this->messageManager->addSuccess(__('Total : <strong>%1</strong> url(s) supprimée(s).', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
