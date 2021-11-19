<?php
namespace Origines\CustomMagepalGTM\Helper;

class DataLayerItemPlugin
{
    public function afterGetProductObject(\MagePal\GoogleTagManager\Helper\DataLayerItem $subject, $result, $item, $qty)
    {
        $result['id'] = substr($result['id'] ,0 ,8);
        return $result;
    }
}