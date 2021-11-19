<?php
namespace Origines\CustomReview\Helper;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	/** CONST for API */
	const ENABLE_API_TRUSTPILOT = 'apireviewsys/config_api_trustpilot/enable_api_trustpilot';
	const URLBASE_API_TRUSTPILOT = 'apireviewsys/config_api_trustpilot/urlbase_api_trustpilot';
	const APIKEY_TRUSTPILOT = 'apireviewsys/config_api_trustpilot/apikey_trustpilot';
	const PASS_SECRET_TRUSTPILOT = 'apireviewsys/config_api_trustpilot/pass_secret_trustpilot';
	const USERNAME_LOGIN_TRUSTPILOT = 'apireviewsys/config_api_trustpilot/username_login_trustpilot';
	const PASS_LOGIN_TRUSTPILOT = 'apireviewsys/config_api_trustpilot/pass_login_trustpilot';
	const NAME_TRUSTPILOT = 'apireviewsys/config_api_trustpilot/name_trustpilot';

	/** CONST for Widget TrustBox */
	const DISPLAY_TRUSTBOX_IS_EMPTY = 'apireviewsys/config_widget_trustbox/display_trustbox_trustpilot_empty';
	const STYLE_HEIGHT_TRUSTBOX = 'apireviewsys/config_widget_trustbox/data_style_height';

	/**
     * @var Curl
     */
	protected $_curl;

	/**
	 * @var Registry
	 */
	protected $_coreRegistry;

	/**
	 * @var Json
	 */
	protected $_serializerJson;

	/**
	 * @var StoreManagerInterface
	 */
	protected $_storeManager;
	
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\HTTP\Client\Curl $curl,
		\Magento\Framework\Serialize\Serializer\Json $serializerJson,
		StoreManagerInterface $storeManager,
		Registry $coreRegistry
	) {
		$this->_curl = $curl;
		$this->_serializerJson = $serializerJson;
		$this->_coreRegistry = $coreRegistry;
		$this->_storeManager = $storeManager;
		parent::__construct($context);
	}

	public function getConfigValueByPath($configPath)
    {
        $value = $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE
        );

		return $value;
    }

	public function isEnableApiTrustpilot()
	{
		if ($this->getConfigValueByPath(self::ENABLE_API_TRUSTPILOT)) {
			return true;
		}
		return false;
	}

	public function getValuesConfigApi()
	{
		return [
			'url_base' => $this->getConfigValueByPath(self::URLBASE_API_TRUSTPILOT),
			'api_key' => $this->getConfigValueByPath(self::APIKEY_TRUSTPILOT),
			'secret' => $this->getConfigValueByPath(self::PASS_SECRET_TRUSTPILOT),
			'name_trustpilot' => $this->getConfigValueByPath(self::NAME_TRUSTPILOT),
			'username-login' => $this->getConfigValueByPath(self::USERNAME_LOGIN_TRUSTPILOT),
			'pass-login' => $this->getConfigValueByPath(self::PASS_LOGIN_TRUSTPILOT),
		];		
	}

	public function displayTrustboxIsEmpty()
	{
		return $this->getConfigValueByPath(self::DISPLAY_TRUSTBOX_IS_EMPTY);
	}

	public function getStyleHeightTrustbox()
	{
		return $this->getConfigValueByPath(self::STYLE_HEIGHT_TRUSTBOX);
	}

	/**
     * Get Store code
     * @return string
     */
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }

	/**
     * Get product Sku
     *
     * @return string|null
     */
    public function getProductSku()
    {
        $product = $this->_coreRegistry->registry('product');
        return $product ? $product->getSku() : null;
    }

	public function jsonDecode($data)
	{
		return $this->_serializerJson->unserialize($data);
	}

	public function jsonEncode($data)
	{
		return $this->_serializerJson->serialize($data);
	}

	public function sendHttpRequest($method, $url, $authentication = false, $contentType = false, $params = [])
	{
		$this->_curl->setOption(CURLOPT_TIMEOUT, 60);
		$this->_curl->setOption(CURLOPT_RETURNTRANSFER, true);
		
		if ($method == 'GET') {
			$this->_curl->get($url);
		}

		if ($method == 'POST') {
			/** build Headers */
			$headers = [];

			if ($authentication) {
				$headers['Authorization'] = $authentication;
			}

			if ($contentType) {
				$headers['Content-Type'] = $contentType;
			}

			$this->_curl->setHeaders($headers);
			$this->_curl->post($url, $params);
		}

		try {
			$result = $this->jsonDecode($this->_curl->getBody());
        } catch (Exception $e) {
            $result['message'] = $e->getMessage();
        }
		return $result;
	}
}