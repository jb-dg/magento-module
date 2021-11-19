<?php
namespace Origines\CronImport\Controller\Adminhtml\Import;

class DeleteAttributeOptions extends \Magento\Framework\App\Action\Action
{
    const FILE_DELETE_OPTIONS = 'delete_options.csv';

    /**
     * @var ImportAll
     */
    protected $_importAll;

    /**
     * @var AttributeOption
     */
    private $_attributeOptionModel;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Origines\CronImport\Model\Import\ImportAll $importAll,
        \Origines\CronImport\Model\AttributeOption $attributeOptionModel
    ) {
        $this->_importAll = $importAll;
        $this->_attributeOptionModel = $attributeOptionModel;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->_importAll->process(self::FILE_DELETE_OPTIONS, $this->_attributeOptionModel, true);
            $this->messageManager->addSuccess("Import Réussi : Attributes options supprimés !");
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $resultRedirect->setPath('*/index/index');
    }
}
