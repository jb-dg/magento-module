<?php
namespace Origines\CronImport\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class ProductOptionHelper extends AbstractHelper
{
    const DEFAULT_STORE_ID = '0';

    /**
     * @var []
     */
    private $options;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeOptionManagementInterface
     */
    private $optionManagement;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\OptionFactory
     */
    private $attributeOptionFactory;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    private $productAttributeRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\OptionLabelFactory
     */
    private $attributeOptionLabelFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory
     */
    private $attrOptionCollectionFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Catalog\Api\ProductAttributeOptionManagementInterface $optionManagement,
        \Magento\Eav\Model\Entity\Attribute\OptionFactory $attributeOptionFactory,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $productAttributeRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Entity\Attribute\OptionLabelFactory $attributeOptionLabelFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
    ) {
        parent::__construct($context);
        $this->optionManagement = $optionManagement;
        $this->attributeOptionFactory = $attributeOptionFactory;
        $this->productAttributeRepository = $productAttributeRepository;
        $this->storeManager = $storeManager;
        $this->attributeOptionLabelFactory = $attributeOptionLabelFactory;
        $this->attrOptionCollectionFactory = $attrOptionCollectionFactory;
    }

    /**
     * @param [] $data
     * @param boolean $isDefault
     * @return AttributeOptionInterface
     */
    public function createAttributeOption($data, $isDefault)
    {
        if (isset($data[0])) {
            $attributeOption = $this->attributeOptionFactory->create();

            $_optionLabels = [];
            foreach ($data as $_storeId => $_label) {
                $_optionLabels[] = $this->createAttributeOptionLabel(['store_id' => $_storeId, 'label' => $_label]);
            }

            return $attributeOption->setIsDefault($isDefault)->setStoreLabels($_optionLabels);
        }

        return null;
    }

    /**
     * @param [] $data
     * @return AttributeOptionLabelInterface
     */
    public function createAttributeOptionLabel($data)
    {
        $attributeOptionLabel = $this->attributeOptionLabelFactory->create();

        return $attributeOptionLabel->setData($data);
    }

    /**
     * @param string $attributeCode
     * @param AttributeOptionInterface $option
     * @return bool
     */
    public function addOption($attributeCode, $option)
    {
        return $this->optionManagement->add($attributeCode, $option);
    }

    public function updateOption($attribute, $optionId, $optionLabel)
    {
        $option = [];
        $option[$optionId] = $optionLabel;
        $attribute->setData('option', [
            'value' => $option
        ]);
        $attribute->save();
    }

    /**
     * @param string $attributeCode
     * @param int $optionId
     * @return bool
     */
    public function deleteOption($attributeCode, $optionId)
    {
        return $this->optionManagement->delete($attributeCode, $optionId);
    }

    /**
     * get options
     *
     * @param $attribute
     * @return array
     */
    public function getOptionsByAttribute($attribute)
    {
        $this->options = $this->getOptionLabels($attribute->getId());
        return $this->options;
    }

    public function getOptionLabels($attributeId)
    {
        $valuesCollection = $this->attrOptionCollectionFactory->create();
        $valuesCollection->setAttributeFilter($attributeId);

        foreach ($this->storeManager->getStores(true) as $store) {
            $_storeId = $store->getId();
            $tableAlias = 'eaov'. $_storeId;
            $valuesCollection->getSelect()->joinLeft( // @codingStandardsIgnoreLine: use sql in helper
                [$tableAlias => $valuesCollection->getTable('eav_attribute_option_value')],
                $tableAlias . '.option_id = main_table.option_id AND '.$tableAlias.'.store_id = ' . $_storeId,
                $tableAlias . '.value AS label'. $_storeId
            );

            $swatchTableAlias = 'eaos'. $_storeId;
            $valuesCollection->getSelect()->joinLeft( // @codingStandardsIgnoreLine: use sql in helper
                [$swatchTableAlias => $valuesCollection->getTable('eav_attribute_option_swatch')],
                $swatchTableAlias . '.option_id = main_table.option_id AND '
                . $swatchTableAlias . '.store_id = ' . $_storeId,
                $swatchTableAlias . '.value AS swatch'. $_storeId
            );
        }

        $valuesCollection->load();

        $values = [];
        foreach ($valuesCollection as $item) {
            $values[$item->getData('label' . self::DEFAULT_STORE_ID)] = $item->getData();
        }

        return $values;
    }
}
