<?php
namespace Origines\UrlCleaner\Block\Adminhtml;

class UrlCleaner extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_urlcleaner';
        $this->_blockGroup = 'Origines_UrlCleaner';
        parent::_construct();
    }
}
