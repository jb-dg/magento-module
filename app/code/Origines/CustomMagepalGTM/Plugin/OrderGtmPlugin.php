<?php
namespace Origines\CustomMagepalGTM\Plugin;

class OrderGtmPlugin
{
    public function afterGetOrderLayer(\MagePal\GoogleTagManager\Model\Order $subjet, $result)
    {
        if (isset($result[0]['order']['items']) && !empty($result[0]['order']['items'])) {
            $items = $result[0]['order']['items'];

            $result[0]['order']['aw_merchant_id'] = 7392498;
            $result[0]['order']['aw_feed_country'] = "FR";
            $result[0]['order']['aw_feed_language'] = "FR";    

            foreach ($items as $key => $item) {

                $newSkuFluxItems = substr($item['id'] ,0 ,8);
                $result[0]['order']['items'][$key]['id'] = (string) $newSkuFluxItems;
                $result[0]['order']['items'][$key]['product_id'] = (string) $newSkuFluxItems;

                if (isset($result[0]['ecommerce']['purchase']['products'][$key]['id'])
                    && !empty($result[0]['ecommerce']['purchase']['products'][$key]['id'])
                ) {
                    $newSkuFluxProducts = substr($result[0]['ecommerce']['purchase']['products'][$key]['id'] ,0 ,8);
                    $result[0]['ecommerce']['purchase']['products'][$key]['id'] = (string) $newSkuFluxProducts;
                }
            }
        }
        return $result;
    }
}