<?php
namespace Origines\CronImport\Helper;

use Magento\Catalog\Api\AttributeSetRepositoryInterface;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    /**
     * @var []
     */
    private $list;

    /**
     * @var AttributeSetRepositoryInterface
     */
    private $attributeSetRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    public function __construct(
        Context $context,
        AttributeSetRepositoryInterface $attributeSetRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        EavSetup $eavSetup,
        ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($context);
        $this->attributeSetRepository = $attributeSetRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->eavSetup = $eavSetup;
        $this->productRepository = $productRepository;
    }

    /**
     * set data to object
     * from format value_id to setValueId()
     *
     * @param $object
     * @param $data
     */
    public function setDataToObject($object, $data)
    {
        foreach ($data as $key => $value) {
            $key = implode('', array_map('ucfirst', explode('_', $key)));
            $function = 'set'. $key;
            if (method_exists($object, $function)) {
                $object->$function($value);
            }
        }
    }

    /**
     * format attribute code
     *
     * @param $string
     * @return mixed
     */
    public function formatAttributeCode($string)
    {
        $string = $this->removeUnicodeWhitespace($string);
        $string = $this->utf8ToSimilarAscii($string);
        $string = $this->removeSpecialCharacter($string, '_');
        $string = preg_replace('/\_+/', '_', $string);
        $string = strtolower($string);

        return $string;
    }

    /**
     * get list of attribute sets
     *
     * @return array
     */
    public function getListAttributeSets()
    {
        $attributeSets = $this->attributeSetRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        foreach ($attributeSets as $attributeSet) {
                $this->list[] = $attributeSet->getAttributeSetName();
        }

        return $this->list;
    }

    /**
     * get group value
     *
     * @param $attributeSet
     * @param $attributeGroup
     * @return string
     */
    public function getGroupValue($attributeSet, $attributeGroup)
    {
        $groupId = $this->getAttributeGroupIdIsExist($attributeSet, $attributeGroup);
        // create a new group if it isn't exist
        if (!$groupId) {
            $this->addAttributeGroup($attributeSet, $attributeGroup);
            $groupId = $this->getAttributeGroupIdIsExist($attributeSet, $attributeGroup);
        }
        return $groupId;
    }

    /**
     * get attribute group id
     *
     * @param $attributeSet
     * @param $attributeGroup
     * @return string
     */
    public function getAttributeGroupIdIsExist($attributeSet, $attributeGroup)
    {
        return $groupId = $this->eavSetup->getAttributeGroup(
            ProductAttributeInterface::ENTITY_TYPE_CODE,
            $attributeSet,
            $attributeGroup,
            'attribute_group_id'
        );
    }

    public function addAttributeGroup($attributeSet, $attributeGroup)
    {
        return $this->eavSetup->addAttributeGroup(
            ProductAttributeInterface::ENTITY_TYPE_CODE,
            $attributeSet,
            $attributeGroup
        );
    }

    /**
     * @param $unicodeString
     *
     * @return mixed
     */
    public function removeUnicodeWhitespace($unicodeString)
    {
        return preg_replace('/[\pZ\pC]/u', '', $unicodeString);
    }

    /**
     * @param $string
     * @param string $replacement
     * @return mixed
     */
    public function removeSpecialCharacter($string, $replacement = '')
    {
        return preg_replace('/[^A-Za-z0-9\_]/', $replacement, $string);
    }

    /**
     * @param string $unicodeString
     *
     * @return mixed
     */
    public function utf8ToSimilarAscii($unicodeString)
    {
        $unicodeString = preg_replace("/[áàâãªä]/u", "a", $unicodeString);
        $unicodeString = preg_replace("/[ÁÀÂÃÄ]/u", "A", $unicodeString);
        $unicodeString = preg_replace("/[ÍÌÎÏ]/u", "I", $unicodeString);
        $unicodeString = preg_replace("/[íìîï]/u", "i", $unicodeString);
        $unicodeString = preg_replace("/[éèêë]/u", "e", $unicodeString);
        $unicodeString = preg_replace("/[ÉÈÊË]/u", "E", $unicodeString);
        $unicodeString = preg_replace("/[óòôõºö]/u", "o", $unicodeString);
        $unicodeString = preg_replace("/[ÓÒÔÕÖ]/u", "O", $unicodeString);
        $unicodeString = preg_replace("/[úùûü]/u", "u", $unicodeString);
        $unicodeString = preg_replace("/[ÚÙÛÜ]/u", "U", $unicodeString);
        $unicodeString = preg_replace("/[’‘‹›‚]/u", "'", $unicodeString);
        $unicodeString = preg_replace("/[“”«»„]/u", '"', $unicodeString);

        $unicodeString = str_replace("–", "-", $unicodeString);
        $unicodeString = str_replace(" ", " ", $unicodeString);
        $unicodeString = str_replace("ç", "c", $unicodeString);
        $unicodeString = str_replace("Ç", "C", $unicodeString);
        $unicodeString = str_replace("ñ", "n", $unicodeString);
        $unicodeString = str_replace("Ñ", "N", $unicodeString);

        return $unicodeString;
    }
}
