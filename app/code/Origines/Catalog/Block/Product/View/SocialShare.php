<?php
namespace Origines\Catalog\Block\Product\View;

class SocialShare extends \Magento\Framework\View\Element\Template
{
    const BASE_URL_FACEBOOK = 'https://www.facebook.com/sharer/sharer.php';
    const BASE_URL_PINTEREST = 'https://pinterest.com/pin/create/button/';

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function getCurrentUrl()
    {
        return $this->_storeManager->getStore()->getCurrentUrl(false);
    }

    public function getScopeConfigValue($configPath)
    {
        return $this->_scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);
    }

    public function getSocials()
    {
        $currentUrlProduct = $this->getCurrentUrl();
        $socials = [
            [
            'icon-class'   => "icon-myo-facebook",
            'href'		    => self::BASE_URL_FACEBOOK.'?u='.$currentUrlProduct,
            'title'          => ''
            ],
            [
                'icon-class'   => "icon-myo-pinterest",
                'href'		    => self::BASE_URL_PINTEREST.'?url='.$currentUrlProduct,
                'title'          => ''
            ],
            [
                'icon-class'   => "icon-myo-share",
                'href'		    => 'mailto:info@example.com?&subject=&cc=&bcc=&body='.$currentUrlProduct.'%0A',
                'title'          => ''
            ],
            // Temporaire le temps de trouver pour Insta
            /*[
                'icon-class'   => "icon-myo-instagram",
                'href'		    => '',
                'title'          => ''
            ]*/
        ];

        return $socials;
    }
}
