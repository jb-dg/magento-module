<?php
namespace Origines\ApiNewsletter\Plugin;

use Mageplaza\Webhook\Model\Config\Source\HookType;
use Magento\Store\Model\ScopeInterface;

class HelperData
{
    const ENABLE_COUPON_CODE_AC = 'apinewslettersys/config_coupon_code/enable_coupon_code_ac';
    const ID_CUSTOM_FIELD_AC = 'apinewslettersys/config_coupon_code/id_custom_field_ac';
    const COUPON_CODE_RULE_ID = 'apinewslettersys/config_coupon_code/coupon_code_rule_id';
    const COUPON_CODE_QTY = 'apinewslettersys/config_coupon_code/coupon_code_qty';
    const COUPON_CODE_LENGTH = 'apinewslettersys/config_coupon_code/coupon_code_length';
    const COUPON_CODE_PREFIX = 'apinewslettersys/config_coupon_code/coupon_code_prefix';
    const COUPON_CODE_SUFFIX = 'apinewslettersys/config_coupon_code/coupon_code_suffix';
    const COUPON_CODE_FORMAT = 'apinewslettersys/config_coupon_code/coupon_code_format';

    protected $_massgenerator;
    protected $_serializerJson;
    protected $_scopeConfig;

    public function __construct(
        \Magento\SalesRule\Model\Coupon\Massgenerator $massgenerator,
        \Magento\Framework\Serialize\Serializer\Json $serializerJson,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_massgenerator = $massgenerator;
        $this->_serializerJson = $serializerJson;
        $this->_scopeConfig = $scopeConfig;
    }

    public function beforeSendHttpRequestFromHook(
        \Mageplaza\Webhook\Helper\Data $subject,
        $hook,
        $item = false,
        $log = false
    ) {
        $isCouponCodeEnable = $this->getScopeConfigValue(self::ENABLE_COUPON_CODE_AC);

        if ($hook->getHookType() === HookType::SUBSCRIBER && $isCouponCodeEnable) {
            $idCustomFieldAc = $this->getScopeConfigValue(self::ID_CUSTOM_FIELD_AC);
            $couponCode = $this->createOneCoupon();
            $hookBody = $this->_serializerJson->unserialize($hook->getBody());
            $hookBody['contact']['fieldValues'][] = ['field' => $idCustomFieldAc, 'value' => $couponCode];
            $hookBody = $this->_serializerJson->serialize($hookBody);
            $hook->setBody($hookBody);
        }

       return [$hook ,$item ,$log];
    }

    public function getScopeConfigValue($configPath)
    {
        return $this->_scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);
    }

    protected function createOneCoupon()
    {
        try
        {
            $couponRuleId = $this->getScopeConfigValue(self::COUPON_CODE_RULE_ID);
            $couponQty = $this->getScopeConfigValue(self::COUPON_CODE_QTY);
            $couponLength = $this->getScopeConfigValue(self::COUPON_CODE_LENGTH);
            $couponPrefix = $this->getScopeConfigValue(self::COUPON_CODE_PREFIX);
            $couponSuffix = $this->getScopeConfigValue(self::COUPON_CODE_SUFFIX);
            $couponFormat = $this->getScopeConfigValue(self::COUPON_CODE_FORMAT);
            
            $data = [
                'rule_id' => $couponRuleId,
                'qty' => $couponQty,
                'length' => $couponLength,
                'format' => $couponFormat,
                'prefix' => $couponPrefix,
                'suffix' => $couponSuffix,
                'dash'=> 0
            ];

            if (!$this->_massgenerator->validateData($data)) {
               return false;
            } else {
                $this->_massgenerator->setData($data);
                $this->_massgenerator->generatePool();
                $generated = $this->_massgenerator->getGeneratedCount();
                $codes = $this->_massgenerator->getGeneratedCodes();
                return $codes[0];

            }

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return false;
        }
    }
}
