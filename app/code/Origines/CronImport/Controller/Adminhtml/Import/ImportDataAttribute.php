<?php
namespace Origines\CronImport\Controller\Adminhtml\Import;

use Origines\CronImport\Model\Attribute as AttributeModel;
use Origines\CronImport\Model\AttributeSet as AttributeSetModel;
use Origines\CronImport\Model\AttributeOption as AttributeOptionModel;

class ImportDataAttribute extends \Magento\Framework\App\Action\Action
{
    const FILE_ATTRIBUTE_SET = 'setAttribute.csv';
    const FILE_ATTRIBUTE = 'attribute.csv';
    const FILE_ATTRIBUTE_OPTIONS= 'attribute_options.csv';

    /**
     * @var ImportAll
     */
    protected $_importAll;

    /**
     * @var AttributeModel
     */
    protected $_attributeModel;

    /**
     * @var AttributeSetModel
     */
    protected $_attributeSetModel;

    /**
     * @var AttributeOptionModel
     */
    protected $_attributeOptionModel;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Origines\CronImport\Model\Import\ImportAll $importAll,
        AttributeModel $attributeModel,
        AttributeSetModel $attributeSetModel,
        AttributeOptionModel $attributeOptionModel
    ) {
        $this->_importAll = $importAll;
        $this->_attributeModel = $attributeModel;
        $this->_attributeSetModel = $attributeSetModel;
        $this->_attributeOptionModel = $attributeOptionModel;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->_importAll->process(self::FILE_ATTRIBUTE_SET, $this->_attributeSetModel, false);
            $this->_importAll->process(self::FILE_ATTRIBUTE, $this->_attributeModel, false);
            $this->_importAll->process(self::FILE_ATTRIBUTE_OPTIONS, $this->_attributeOptionModel, false);
            // go to grid
            $this->messageManager->addSuccess("Import Réussi !");

        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        return $resultRedirect->setPath('*/index/index');
    }
}
