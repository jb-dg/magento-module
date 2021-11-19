<?php
namespace Origines\CronImport\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Psr\Log\LoggerInterface;
use Origines\CronImport\Helper\ProductOptionHelper;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Store\Api\StoreRepositoryInterface;

class AttributeOption extends Import
{
    /**
     * @var ProductOptionHelper
     */
    public $productOptionHelper;

    private $_productAttributeRepository;

    private $attribute;

    /**
     * @var StoreRepositoryInterface
     */
    private $_storeRepository;

    public function __construct(
        LoggerInterface $logger,
        Filesystem $filesystem,
        ProductOptionHelper $productOptionHelper,
        Repository $attributeRepository,
        StoreRepositoryInterface $storeRepository,
        array $data = []
    ) {
        parent::__construct($logger, $filesystem, $data);
        $this->productOptionHelper = $productOptionHelper;
        $this->_productAttributeRepository = $attributeRepository;
        $this->_storeRepository = $storeRepository;
    }

    public function getProductAttributeByCode($code)
    {
        $attribute = $this->_productAttributeRepository->get($code);
        return $attribute;
    }

    /**
     * Import Attribute Option
     * Cannot replace the exist values
     *
     * @param $newOption
     * @throws LocalizedException
     */
    public function importAttributeOption($newOption)
    {
        $attributeCode = $newOption['attribute_code'];
        unset($newOption['attribute_code']);
        $attribute = $this->getProductAttributeByCode($attributeCode);

        if (!isset($newOption['admin'])) {
            throw new LocalizedException(__('Default value not found'));
        }

        $newOptionDefault = $newOption['admin'];    
        $options = $this->productOptionHelper->getOptionsByAttribute($attribute);
        $isUpdate = array_key_exists($newOptionDefault, $options);
        $isUpdate ? $optionId = $options[$newOptionDefault]['option_id'] :  $optionId =  '';       

        $newsLabelsOption = [];
        foreach ($newOption as $key => $oneOptionValue) {
            $storeId = $this->getIdStoreByCode($key);
            if (empty($oneOptionValue) && $isUpdate && !empty($optionId)) {
                $label = 'label'.$storeId;
                if ($options[$newOptionDefault][$label] === null){
                    $currentOptionValue = '';
                } else {
                    $currentOptionValue = $attribute->setStoreId($storeId)
                        ->getSource()
                        ->getOptionText($optionId);
                }
                $oneOptionValue = $currentOptionValue;
            }
            $newsLabelsOption[$storeId] = $oneOptionValue;
        }

        if (!$isUpdate && $_optionObj = $this->productOptionHelper->createAttributeOption($newsLabelsOption, false)) {
            $this->productOptionHelper->addOption($attributeCode, $_optionObj);
        } else {
            $this->productOptionHelper->updateOption($attribute, $optionId, $newsLabelsOption);
        }
    }

    public function import($rowData)
    {
        return $this->importAttributeOption($rowData);
    }

    public function delete($rowData)
    {
        return $this->deleteAttributesOptions($rowData);
    }

    public function deleteAttributesOptions($rowData)
    {
        $attributeCode = $rowData['attribute_code'];
        $attribute = $this->getProductAttributeByCode($attributeCode);
        $options = $this->productOptionHelper->getOptionsByAttribute($attribute);

        if (array_key_exists($rowData['admin'], $options)) {
            $optionId = $options[$rowData['admin']]['option_id'];
            $this->productOptionHelper->deleteOption($attributeCode, $optionId);
        }
    }

    public function getIdStoreByCode($storeCode)
    {
        try {
            $store = $this->_storeRepository->get($storeCode);
            return $store->getId();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return 0;
        }
    }
}
