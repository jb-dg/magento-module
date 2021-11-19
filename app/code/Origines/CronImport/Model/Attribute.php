<?php
namespace Origines\CronImport\Model;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Framework\Filesystem;
use Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory;
use Psr\Log\LoggerInterface;
use Origines\CronImport\Helper\Data;
use Origines\CronImport\Helper\ProductOptionHelper;

class Attribute extends Import
{
    const DEFAULT_ATTRIBUTE_SET = 'Default';
    const ALL_ATTRIBUTE_SETS_VALUE = 'all';
    const DEFAULT_ATTRIBUTE_GROUP = 'General';

    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var ProductOptionHelper
     */
    private $productOptionHelper;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var AttributeFactory
     */
    private $attributeFactory;

    public function __construct(
        LoggerInterface $logger,
        Filesystem $filesystem,
        ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Eav\Setup\EavSetup $eavSetup,
        Data $helper,
        ProductOptionHelper $productOptionHelper,
        AttributeFactory $attributeFactory,
        array $data = []
    ) {
        parent::__construct($logger, $filesystem, $data);

        $this->attributeRepository = $attributeRepository;
        $this->eavSetup = $eavSetup;
        $this->helper = $helper;
        $this->productOptionHelper = $productOptionHelper;
        $this->attributeFactory = $attributeFactory;
    }

    public function delete($data)
    {
        // TODO: Implement delete() method.
        return "";
    }

    /**
     * import attribute
     *
     * @param $data
     * @return bool
     */
    public function import($data)
    {
        $data = $this->_prepareData($data);
        unset($data['options']);
        if ($attribute = $this->saveAttribute($data)) {
            $this->assignAttributeSet($data, $attribute->getAttributeCode());
            return true;
        }

        return false;
    }

    /**
     * prepare data for importing
     *
     * @param $data
     * @return mixed
     */
    private function _prepareData($data)
    {
        $frontendLabels = [];
        if (isset($data['frontend_labels'])) {
            unset($data['frontend_labels']);
        }
        if (!empty($data['default_label'])) {
            $frontendLabels = $data['default_label'];
            unset($data['default_label']);
        }
        $data['default_frontend_label'] = $frontendLabels;

        if (isset($data['attribute_options'])) {
            unset($data['attribute_options']);
        }

        return $data;
    }

    /**
     * assign attribute set to attribute
     *
     * @param $data
     * @param $attributeCode
     */
    private function assignAttributeSet($data, $attributeCode)
    {
        $attributeSets = [self::DEFAULT_ATTRIBUTE_SET];
        if (!empty($data['attribute_set'])) {
            $attributeSets = explode('|', $data['attribute_set']);
        }
        if (in_array(self::ALL_ATTRIBUTE_SETS_VALUE, array_map('strtolower', $attributeSets))) {
            $attributeSets = $this->helper->getListAttributeSets();
        }

        $attributeGroup = self::DEFAULT_ATTRIBUTE_GROUP;
        if (!empty($data['attribute_group_name'])) {
            $attributeGroup = $data['attribute_group_name'];
        }

        // assign to attribute set
        foreach ($attributeSets as $attributeSet) {
            try {
                $groupValue = $this->helper->getGroupValue($attributeSet, $attributeGroup);
                $this->eavSetup->addAttributeToGroup(
                    ProductAttributeInterface::ENTITY_TYPE_CODE,
                    $attributeSet,
                    $groupValue,
                    $attributeCode
                );
            } catch (\Exception $e) {
                $this->_logger->error($e->getMessage());
            }
        }
    }

    /**
     * save attribute
     *
     * @param $data
     * @return bool|ProductAttributeInterface
     */
    private function saveAttribute($data)
    {
        if (empty($data['attribute_code'])) {
            return false;
        }

        $data['attribute_code'] = $this->helper->formatAttributeCode($data['attribute_code']);

        $attribute = null;
        try {
            $attribute = $this->attributeRepository->get($data['attribute_code']);
        } catch (\Exception $e) {
             $this->_logger->error($e->getMessage());
        }
        if (!$attribute) {
            $attribute = $this->attributeFactory->create();
        }

        $this->helper->setDataToObject($attribute, $data);
        try {
            $this->attributeRepository->save($attribute);
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
            return false;
        }

        return $attribute;
    }
}
