<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Origines\CatalogWidget\Block\Adminhtml\Widget;

use Magento\Framework\UrlInterface;

/**
 * Category form input image element
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class ImageViewer extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        UrlInterface $urlBuilder,
        $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
//        $this->setType('file');
    }

    /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';

        if ((string)$this->getValue()) {
            $url = $this->_getUrl();

            if (!preg_match("/^http\:\/\/|https\:\/\//", $url)) {
                $url = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . $url;
            }
            $url = $this->_escaper->escapeUrl($url);

            $html = '<a href="' .
                $url .
                '"' .
                ' onclick="imagePreview(\'' .
                $this->getHtmlId() .
                '_image\'); return false;" ' .
                $this->_getUiId(
                    'link'
                ) .
                '>' .
                '<img src="' .
                $url .
                '" id="' .
                $this->getHtmlId() .
                '_image" title="' .
                $this->_escaper->escapeHtmlAttr($this->getValue()) .
                '"' .
                ' alt="' .
                $this->_escaper->escapeHtmlAttr($this->getValue()) .
                '" height="22" width="22" class="small-image-preview v-middle"  ' .
                $this->_getUiId() .
                ' />' .
                '</a> ';
        }
//        $this->setClass('input-file');
        $html .= parent::getElementHtml();
//        $html .= $this->_getDeleteCheckbox();

        return $html;
    }

    /**
     * Return html code of delete checkbox element
     *
     * @return string
     */
    protected function _getDeleteCheckbox()
    {
        $html = '';
        if ($this->getValue()) {
            $label = (string)new \Magento\Framework\Phrase('Delete Image');
            $html .= '<span class="delete-image">';
            $html .= '<input type="checkbox"' .
                ' name="' .
                parent::getName() .
                '[delete]" value="1" class="checkbox"' .
                ' id="' .
                $this->getHtmlId() .
                '_delete"' .
                ($this->getDisabled() ? ' disabled="disabled"' : '') .
                '/>';
            $html .= '<label for="' .
                $this->getHtmlId() .
                '_delete"' .
                ($this->getDisabled() ? ' class="disabled"' : '') .
                '> ' .
                $label .
                '</label>';
            $html .= $this->_getHiddenInput();
            $html .= '</span>';
        }

        return $html;
    }

    /**
     * Return html code of hidden element
     *
     * @return string
     */
    protected function _getHiddenInput()
    {
        return '<input type="hidden" name="' . parent::getName() . '[value]" value="' .
            $this->_escaper->escapeHtmlAttr($this->getValue()) . '" />';
    }

    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        return $this->getValue();
    }

    /**
     * Return name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * Get the after element Javascript.
     *
     * @return mixed
     */
    public function getAfterElementJs()
    {
        $js = "<script>
                var element = document.getElementById('".$this->getHtmlId()."').onchange=function () {
                    var new_value = this.value;
                    var matches = new_value.match(/(___directive\/)([a-zA-Z0-9,_-]+)/);
                    if (matches && matches[2]) {
                        new_value = matches[2];
                        new_value = Base64.decode(new_value.replace(/,/g,'')).replace('{{media url=\"','').replace('\"}}','');
                        var urlMedia = '".$this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA])."';
                        this.value = new_value;
                        this.parentElement.firstChild.textContent = new_value;
                        var imageElement = document.getElementById('".$this->getHtmlId()."');
                        imageElement.title=new_value;
                        imageElement.alt=new_value;
                        imageElement.src = urlMedia + '/' + new_value;
                    }
                };
               </script>";
        return $js; 
    }
}
