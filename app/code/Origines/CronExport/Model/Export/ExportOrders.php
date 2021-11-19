<?php
namespace Origines\CronExport\Model\Export;

class ExportOrders extends AbstractEntity
{
    const PATH_SAVE_CSV_FILE_ORDERS = "origines/CSV/Orders";
    const PATH_SAVE_CSV_FILE_PRODUCTS_ORDERS = "origines/CSV/Products_Orders";
    const FILE_NAME_ORDERS = "Orders.csv";
    const FILE_NAME_PRODUCTS_ORDERS = "Products_Orders.csv";

    const SELECT_ATTRIBUTES_ORDERS = [
        'entity_id',
        'increment_id',
        'status',
        'customer_email',
        'created_at',
        'sutunam_order_comment',
        'customer_note',
        'coupon_code',
        'shipping_method',
        /** Remise */
        'base_discount_amount',
        'discount_amount',
        /** Sous-total (HT) */
        'base_subtotal',
        'subtotal',
        /** Sous-total (TTC) */
        'base_subtotal_incl_tax',
        'subtotal_incl_tax',
        /** Livraison et traitement (HT) */
        'base_shipping_amount',
        'shipping_amount',
        /** Livraison et traitement (TTC) */
        'base_shipping_incl_tax',
        'shipping_incl_tax',
        /** Total général (TTC) */
        'base_grand_total',
        'grand_total',
        /** dont TVA */
        'base_tax_amount',
        'tax_amount',
        /** Taux devise/EUR */
        'base_to_order_rate',
        /** Currency €/£ */
        'base_currency_code',
        'order_currency_code',
        /** Total payé */
        'base_total_invoiced',
        'total_invoiced',
        'base_total_paid',
        'total_paid',
    ];

    const SELECT_ATTRIBUTES_ORDERS_AWGIFTCARD = [
        'aw_giftcard_amount',
        'base_aw_giftcard_amount',
        'base_aw_giftcard_invoiced',
        'aw_giftcard_invoiced',
        'base_aw_giftcard_refunded',
        'aw_giftcard_refunded'
    ];

    /**
     * @var OrderCollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var ItemCollectionFactory
     */
    protected $_itemCollectionFactory;

    /**
     * @var \Aheadworks\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory
     */
    protected $_orderGiftcardCollectionFactory;

    /**
     * ExportOrders constructor.
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory        $orderCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory   $itemCollection
     * @param \Aheadworks\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory $orderGiftcardCollectionFactory
     * @param \Origines\CronExport\Model\Export\Adapter\Csv                     $exportCsvAdapter
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $itemCollection,
        \Aheadworks\Giftcard\Model\ResourceModel\Giftcard\CollectionFactory $orderGiftcardCollectionFactory,
        Adapter\Csv $exportCsvAdapter
    ) {
        $this->_orderCollectionFactory = $orderCollection;
        $this->_itemCollectionFactory = $itemCollection;
        $this->_orderGiftcardCollectionFactory = $orderGiftcardCollectionFactory->create();
        parent::__construct($exportCsvAdapter);
    }

    /**
     * get Orders data by Status Pending, processing and payment review
     * @return array
     */
    public function getOrdersByStatus()
    {
        return $orderCollection = $this->_orderCollectionFactory->create()
            ->addFieldToSelect(self::SELECT_ATTRIBUTES_ORDERS)
            ->addFieldToSelect(self::SELECT_ATTRIBUTES_ORDERS_AWGIFTCARD)
            ->addAttributeToFilter('status', ['in'=> ['processing','pending','payment_review','hipay_authorized']])
            ->addAttributeToFilter('created_at', ['gteq' => date("Y-m-d h:i:s", strtotime('-5 days'))])
            ->load();
    }

    public function getOrdersGifcard()
    {
        $ordersIds = [];
        $orders = $this->_orderGiftcardCollectionFactory
            ->addFieldToSelect('order_id')
            ->load();

        foreach ($orders as $value) {
            if (!empty($value->getData('order_increment_id'))) {
                $ordersIds[] = $value->getData('order_increment_id');
            }
        }
        return $ordersIds;
    }

    public function getOrdersGifcardComplete()
    {
        $ordersIds = $this->getOrdersGifcard();
        if (!empty($ordersIds)) {
            return $orderCollection = $this->_orderCollectionFactory->create()
            ->addFieldToSelect(self::SELECT_ATTRIBUTES_ORDERS)
            ->addFieldToSelect(self::SELECT_ATTRIBUTES_ORDERS_AWGIFTCARD)
            ->addAttributeToFilter('status', ['eq'=> 'complete'])
            ->addAttributeToFilter('increment_id', ['in'=> $ordersIds])
            ->addAttributeToFilter('created_at', ['gteq' => date("Y-m-d h:i:s", strtotime('-2 days'))])
            ->load();
        }
        return null;
    }

    /**
     * get Billing or Shipping Address data by Order Id to prepareDataOrders
     * @param (integer) $orderId
     * @param (string) $type
     * @return array
     */
    public function getAddressByOrder($order, $type)
    {
        $dataAddress = [];
        $newtab = [];

        if ($type == 'billing') {
            $address = $order->getBillingAddress();
            $type = 'facturation';
        } elseif ($type == 'shipping') {
            $address = $order->getShippingAddress();
            $type = 'livraison';
        }
        
        if (isset($address)) {
            //get street line 1 and 2
            $addressStreet = $address->getStreet();
            $addressStreetLine1 = isset($addressStreet[0]) ? $addressStreet[0] : '';
            $addressStreetLine2 = isset($addressStreet[1]) ? $addressStreet[1] : '';
            $addressStreetLine3 = isset($addressStreet[2]) ? $addressStreet[2] : '';

            //colissimo
            $colissimoPickupId = !empty($address->getDataUsingMethod('colissimo_pickup_id')) ?
                $address->getDataUsingMethod('colissimo_pickup_id') : '';
            $colissimoProductCode = !empty($address->getDataUsingMethod('colissimo_product_code')) ?
                $address->getDataUsingMethod('colissimo_product_code') : '';
            $colissimoNetworkCode = !empty($address->getDataUsingMethod('colissimo_network_code')) ?
                $address->getDataUsingMethod('colissimo_network_code') : '';

            $dataAddress = [
                'adresse_'.$type.'_prefix' => $address->getPrefix(),
                'adresse_'.$type.'_lastname' => $address->getLastName(),
                'adresse_'.$type.'_firstname' => $address->getFirstName(),
                'adresse_'.$type.'_company'  => $address->getCompany(),
                'adresse_'.$type.'_street_ligne1' => $addressStreetLine1,
                'adresse_'.$type.'_street_ligne2' => $addressStreetLine2,
                'adresse_'.$type.'_street_ligne3' => $addressStreetLine3,
                'adresse_'.$type.'_postcode'  => $address->getPostCode(),
                'adresse_'.$type.'_city'  => $address->getCity(),
                'adresse_'.$type.'_region' => $address->getRegion(),
                'adresse_'.$type.'_country_id' => $address->getCountryId(),
                'adresse_'.$type.'_telephone' => $address->getTelephone(),
                $type.'_colissimo_pickup_id' => $colissimoPickupId,
                $type.'_colissimo_product_code' => $colissimoProductCode,
                $type.'_colissimo_network_code' => $colissimoNetworkCode
            ];
        }

        return $dataAddress;
    }

    /**
     * prepare orders data to orders.csv
     * @return array
     */
    public function prepareDataOrders($orderCollection)
    {
        $data = [];
        $dataProductsByOrder = [];

        if ($orderCollection !== null) {
            foreach ($orderCollection as $order) {
                $orderId = $order->getEntityId();
                $orderDataBase = $order->getData();
                
                $orderDataBase["payment_method"] = (string) $order->getPayment()->getMethod();
                if(($orderDataBase["payment_method"] == "hipay_cc" && $orderDataBase['status'] == 'pending') === false){

                    $orderBillingAddresss = $this->getAddressByOrder($order, 'billing');
                    $orderShippingAddress = $this->getAddressByOrder($order, 'shipping');
        
                    // Delete line ending Unix and Windows and | on address
                    $orderDataBase = str_replace(["\r\n","\t","\n","|"], "", $orderDataBase);
                    $orderBillingAddresss = str_replace(["\r\n","\t","\n"], "", $orderBillingAddresss);
                    $orderShippingAddress = str_replace(["\r\n","\t","\n"], "", $orderShippingAddress);
                    $orderBillingAddresss = str_replace("|", ",", $orderBillingAddresss);
                    $orderShippingAddress = str_replace("|", ",", $orderShippingAddress);

                    $orderAllAddress = $orderBillingAddresss + $orderShippingAddress;
                    $valuesDataOrder = $orderDataBase + $orderAllAddress;
                    $data[] = $valuesDataOrder;
        
                    $itemsOrder = $order->getAllVisibleItems();
                    $allItemsDataByOrder[] = $this->prepareDataProductsByOrders($itemsOrder);
                }
            }
        
            // create array $dataProductsByOrder without the multidimensional from array $allItemsDataByOrder
            if (!empty($allItemsDataByOrder)) {
                foreach ($allItemsDataByOrder as $itemsData) {
                    foreach ($itemsData as $itemData) {
                        $dataProductsByOrder[] = $itemData;
                    }
                }
            }
        }
        return ['orders' => $data, 'products_orders' => $dataProductsByOrder];
    }

    /**
     * prepare items Data by Order to products_orders.csv
     * @return array
     */
    public function prepareDataProductsByOrders($itemsOrder)
    {
        $allItems = [];
        foreach ($itemsOrder as $key => $item) {

            $itemData = [
                'order_id'              => $item->getDataUsingMethod('order_id'),
                'name'                  => $item->getDataUsingMethod('name'),
                'sku'                   => $item->getDataUsingMethod('sku'),
                'product_type'          => $item->getDataUsingMethod('product_type'),
                'qty_ordered'           => round($item->getDataUsingMethod('qty_ordered')),
                'gift_wrap'             => $item->getDataUsingMethod('gift_wrap'),
                'gift_comment'          => str_replace(["\r\n","\n"], "<br>", $item->getDataUsingMethod('gift_comment')),
                'base_price_incl_tax'   => round($item->getDataUsingMethod('base_price_incl_tax'), 2),
                'price_incl_tax'        => round($item->getDataUsingMethod('price_incl_tax'), 2),
                'base_discount_amount'  => round($item->getDataUsingMethod('base_discount_amount'), 2),
                'discount_amount'       => round($item->getDataUsingMethod('discount_amount'), 2),
                'base_tax_amount'       => round($item->getDataUsingMethod('base_tax_amount'), 2),
                'tax_amount'            => round($item->getDataUsingMethod('tax_amount'), 2),
                'tax_percent'           => round($item->getDataUsingMethod('tax_percent'), 2),
                'product_options'       => $item->getDataUsingMethod('product_options'),
            ];

            if (!isset($itemData['gift_wrap'])) {
                $itemData['gift_wrap'] = 0;
            }

            if (isset($itemData['price_incl_tax']) && isset($itemData['discount_amount'])) {
                $piceInclTax = floatval($itemData['price_incl_tax']);
                $discountAmount = floatval($itemData['discount_amount']);
                $itemData['price_incl_tax'] = $piceInclTax - $discountAmount;
            }

            if (isset($itemData['product_options'])) {
                $itemData['product_options'] = $this->exportCsvAdapter->jsonEncode($itemData['product_options']);
            }

            $itemData = str_replace(["\r\n","\t","\n"], "", $itemData);
            $allItems[] = $itemData;
        }
        return $allItems;
    }

    public function export($download = false, $delimiter = '|')
    {
        $orderCollectionStatus = $this->getOrdersByStatus();
        $orderCollectionStatus = $this->prepareDataOrders($orderCollectionStatus);
        $ordersCollectionGiftcard = $this->getOrdersGifcardComplete();
        $ordersCollectionGiftcard = $this->prepareDataOrders($ordersCollectionGiftcard);

        $dataOrders = array_merge(
            $orderCollectionStatus['orders'],
            $ordersCollectionGiftcard['orders']
        );

        $dataProducstOrders = array_merge(
            $orderCollectionStatus['products_orders'],
            $ordersCollectionGiftcard['products_orders']
        );

        if (!empty($dataOrders) && !empty($dataProducstOrders)) {
            $urlFileOrders = $this->exportCsvAdapter->export(
                $dataOrders,
                self::PATH_SAVE_CSV_FILE_ORDERS,
                self::FILE_NAME_ORDERS
            );

            $urlFileProductsOrders = $this->exportCsvAdapter->export(
                $dataProducstOrders,
                self::PATH_SAVE_CSV_FILE_PRODUCTS_ORDERS,
                self::FILE_NAME_PRODUCTS_ORDERS
            );
            return ['url_file_orders' => $urlFileOrders, 'url_file_products_orders' => $urlFileProductsOrders];
        }
        return null;
    }
}
