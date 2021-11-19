<?php
namespace Origines\CustomMagepalGTM\Block\Data;

class Home extends \Origines\CustomMagepalGTM\Block\Data\CatalogWidget
{
    public function addImpressionList()
    {
        $this->setImpressionList(
            $this->getListType(),
            $this->_eeHelper->getHomeWidgetClassName(),
            $this->_eeHelper->getHomeWidgetContainerClass()
        );
    }

    protected function _init()
    {
        $this->setListType($this->_eeHelper->getHomeWidgetListType());
        $this->getUseWidgetTitle($this->_eeHelper->getHomeWidgetUseWidgetTitle());
        return $this;
    }
}
