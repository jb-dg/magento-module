<?php
namespace Origines\CustomMagepalGTM\Plugin;

class CartPlugin
{
    public function afterGetCart(\MagePal\GoogleTagManager\Model\Cart $subject, $result)
    {
        if (isset($result['items']) && !empty($result['items'])) {
            foreach ($result['items'] as $key => $item) {
                $result['items'][$key]['id'] = substr($item['id'] ,0 ,8);
            }
        }

        return $result;
    }
}