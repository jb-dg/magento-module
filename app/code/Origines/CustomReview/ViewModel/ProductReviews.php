<?php
namespace Origines\CustomReview\ViewModel;

class ProductReviews implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    protected $_helperData;

    public function __construct(
        \Origines\CustomReview\Helper\Data $helperData
    ) {
        $this->_helperData = $helperData;
    }

    public function isEnableApiTrustpilot()
    {
        return $this->_helperData->isEnableApiTrustpilot();
    }

    public function getBusinessUnitId()
	{
		$valuesConfigApi = $this->_helperData->getValuesConfigApi();
		$reponse = null;
		
		if (!in_array(null, $valuesConfigApi)) {
			$url = $valuesConfigApi['url_base']."/business-units/find?apikey=".$valuesConfigApi['api_key']."&name=".$valuesConfigApi['name_trustpilot'];
			$reponse = $this->_helperData->sendHttpRequest('GET', $url);
			if (!empty($reponse) && isset($reponse['id'])) {
				return $reponse['id'];
			} else {
				$reponse = null;
			}
		}
		return $reponse;
	}

	public function getAccessToken($valuesConfigApi)
	{
		$url = "/oauth/oauth-business-users-for-applications/accesstoken";
		$authentication = "Basic ".base64_encode($valuesConfigApi['api_key'] . ":" . $valuesConfigApi['secret']);
		$contentType = 'application/x-www-form-urlencoded';

		$params = [
			'grant_type' => 'password',
			'username' => $valuesConfigApi['username-login'],
			'password' => $valuesConfigApi['pass-login'],
		];

		$reponse = $this->_helperData->sendHttpRequest('POST', $url, $authentication, $contentType, $params);
		
		if (isset($reponse['access_token'])) {
			return $reponse['access_token'];
		}
		return null;
	}

	public function getReviewsTrustpilot()
	{
		$sku = $this->_helperData->getProductSku();
		$valuesConfigApi = $this->_helperData->getValuesConfigApi();
		$businessUnitId = $this->getBusinessUnitId();
		$reponse = null;

		if (!in_array(null, $valuesConfigApi) && isset($businessUnitId)) {
			$sku = "sku=".$sku;
			$url = $valuesConfigApi['url_base'].
				"/product-reviews/business-units/".
				$businessUnitId.
				"/reviews?apikey=".$valuesConfigApi['api_key'].
				"&".$sku;
			$reponse = $this->_helperData->sendHttpRequest('GET', $url);
		}
		return $reponse;
	}

	public function getPrivateProductReviews()
	{
		$sku = $this->_helperData->getProductSku();
		$valuesConfigApi = $this->_helperData->getValuesConfigApi();
		$businessUnitId = $this->getBusinessUnitId();
		$reponse = null;

		if (!in_array(null, $valuesConfigApi) && !empty($businessUnitId)) {
			$url = $valuesConfigApi['url_base']."/private/product-reviews/business-units/".$businessUnitId."/reviews?sku=".$sku;
			$authentication = "Bearer ".$this->getAccessToken($valuesConfigApi);
			$contentType = "application/json";
			$params = [
				'grant_type' => 'password',
				'username' => $valuesConfigApi['username-login'],
				'password' => $valuesConfigApi['pass-login'],
			];
			$reponse = $this->_helperData->sendHttpRequest('POST', $url, $authentication, $contentType, $params);
		}
		return $reponse;
	}

	public function getWidgetProductReviews()
    {
        $currentProductSku = $this->_helperData->getProductSku();
		$displayTustbox = $this->_helperData->displayTrustboxIsEmpty();
	
		if ($displayTustbox) {
			$dataNoReviews = "hide";
		} else {
			$dataNoReviews = "show";
		}

		$reviewsTrustpilot = $this->getReviewsTrustpilot();
				
		if (isset($reviewsTrustpilot['productReviews']) && count($reviewsTrustpilot['productReviews'])) {
			$dataStyleHeight = $this->_helperData->getStyleHeightTrustbox();
		} else {
			$dataStyleHeight = "100%";
		}

		$dataReviewLanguage = $this->_helperData->getStoreCode();
		$dataLocale = $dataReviewLanguage.'-'.strtoupper($dataReviewLanguage);
		$businessUnitId = $this->getBusinessUnitId();
        return $valuesWidget = [
			'data-locale' => $dataLocale,
			'data-businessunit-id' => $businessUnitId,
			'data-sku' => $currentProductSku,
			'data-review-languages' => $dataReviewLanguage,
			'data-no-reviews' => $dataNoReviews,
			'data-style-height' => $dataStyleHeight,
		];
    }
}