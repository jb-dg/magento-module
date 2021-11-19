<?php
namespace Origines\CronExport\Model\Export;

use Magento\Store\Model\Store;
use Magento\Store\Model\ScopeInterface;
use Magento\Newsletter\Model\Subscriber as ModelSubscriber;

class ExportCustomers extends AbstractEntity
{
    const PATH_SAVE_CSV_FILE = 'origines/CSV/Customers';
    const FILE_NAME = 'Customers.csv';
    const PATH_SAVE_CSV_ACTIVE_CAMPAIGN = 'origines/CSV/ActiveCampaign';
    const FILE_NAME_ACTIVE_CAMPAIGN = 'customers_data_origines.csv';
    const SEPERATOR_IN_COLUMN = ",";
    const SETTINGS_CRONEXPORT_NB_PERIOD_SUBSCRIBER = "cronexportsys/config_period_subscriber/nb_period_subscriber_status";
    const SETTINGS_CRONEXPORT_TYPE_PERIOD_SUBSCRIBER = "cronexportsys/config_period_subscriber/type_period_subscriber_status";
    const SETTINGS_CRONEXPORT_NB_PERIOD_ORDER = "cronexportsys/config_period_last_order/nb_period_last_order";
    const SETTINGS_CRONEXPORT_TYPE_PERIOD_ORDER = "cronexportsys/config_period_last_order/type_period_last_order";
    const DEFAULT_PERIOD_SUBSCRIBER = "-5 days";

    const TABLE_NAME_CUSTOMER_ENTITY = 'customer_entity';
    const COLUMN_CUSTOMER_ENTITY = [
        'customer_id' => 'customer_entity.entity_id',
        'customer_email' => 'customer_entity.email',
        'store_id' => 'customer_entity.store_id',
        'customer_prefix' => 'customer_entity.prefix',
        'customer_firstname' => 'customer_entity.firstname',
        'customer_lastname' => 'customer_entity.lastname',
        'customer_dob' => 'customer_entity.dob',
        'customer_created_at' => 'customer_entity.created_at',
        'customer_default_billing' => 'customer_entity.default_billing',
    ];

    const TABLE_NAME_NEWSLETTER_SUBSCRIBER = 'newsletter_subscriber';
    const COLUMN_CUSTOMER_NEWSLETTER_SUBSCRIBER = [
        'subscriber_status' => 'newsletter_subscriber.subscriber_status',
    ];

    const TABLE_NAME_CUSTOMER_ADDRESS_ENTITY = 'customer_address_entity';
    const COLUMN_CUSTOMER_ADDRESS_ENTITY = [
        'customer_address_entity_id' => 'customer_address_entity.entity_id',
        'customer_address_city' => 'customer_address_entity.city',
        'customer_address_country_id' => 'customer_address_entity.country_id',
        'customer_address_postcode' => 'customer_address_entity.postcode',
        'customer_address_telephone' => 'customer_address_entity.telephone'
    ];

    const SELECT_ATTRIBUTES_ORDERS = [
        'entity_id',
        'created_at',
        'grand_total',
    ];

    const TABLE_NAME_SALES_ORDER = 'sales_order';
    const COLUMN_SALES_ORDER = [
        'nb_commandes' => 'sales_order.nb_commandes',
        'CA_total_client' => 'sales_order.CA_total_client',
        'panier_moyen' => 'sales_order.panier_moyen',
        'date_premiere_commande' => 'sales_order.date_premiere_commande',
        'date_derniere_commande' => 'sales_order.date_last_order'
    ];
    const COLUMN_LAST_ORDER = [
        
        'montant_total_derniere_commande' => 'last_order.grand_total',
        'nb_articles_derniere_commande' => 'last_order.total_qty_ordered',
        'id_last_order' => 'last_order.entity_id'
    ];

    const TABLE_NAME_SALES_ORDER_ITEM = 'sales_order_item';
    const TABLE_NAME_CATALOG_CATEGORY_PRODUCT = 'catalog_category_product';
    const TABLE_NAME_CATALOG_CATEGORY_ENTITY_VARCHAR = 'catalog_category_entity_varchar';

    /**
     * @var \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory
     */
    protected $_subcriberCollectionFactory;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $_storeRepository;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $_eavAttribute;

    protected $scopeConfig;

    public function __construct(
        Adapter\Csv $exportCsvAdapter,
        \Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory $subcriberCollectionFactory,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_subcriberCollectionFactory = $subcriberCollectionFactory;
        $this->_storeRepository = $storeRepository;
        $this->_resourceConnection = $resourceConnection;
        $this->_eavAttribute = $eavAttribute;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($exportCsvAdapter);
    }

    public function getScopeConfigValue($configPath)
    {
        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);
    }

    public function getPeriodSubscriber($fieldNbPeriod, $fieldTypePeriod)
    {
        $period = self::DEFAULT_PERIOD_SUBSCRIBER;
        $nbPeriod = (int) $this->getScopeConfigValue($fieldNbPeriod);
        $typePeriod = $this->getScopeConfigValue($fieldTypePeriod);
        if (!empty($nbPeriod) && !empty($typePeriod) && is_int($nbPeriod) && $nbPeriod <= 365) {
            $period = '-'.$nbPeriod.' '.$typePeriod;
        }
        return $period;
    }

    public function getVisitorSubscriber()
    {
        $data = [];
        $dataKey = self::COLUMN_CUSTOMER_ENTITY
            + self::COLUMN_CUSTOMER_NEWSLETTER_SUBSCRIBER
            + self::COLUMN_CUSTOMER_ADDRESS_ENTITY
            + self::COLUMN_SALES_ORDER
            + self::COLUMN_LAST_ORDER;

        $period = $this->getPeriodSubscriber(
            self::SETTINGS_CRONEXPORT_NB_PERIOD_SUBSCRIBER,
            self::SETTINGS_CRONEXPORT_TYPE_PERIOD_SUBSCRIBER
        );

        $period = date("Y-m-d h:i:s", strtotime($period));

        $subscriberCollection = $this->_subcriberCollectionFactory->create()
            ->addFieldToSelect('subscriber_email', 'customer_email')
            ->addFieldToSelect('subscriber_status')
            ->addFieldToSelect('store_id')
            ->addFieldToSelect('customer_id')
            ->addFieldToFilter('change_status_at', ['gteq' => $period])
            ->addFieldToFilter('customer_id', ['eq' => 'O'])
            ->addFieldToFilter('subscriber_status', ['in' => [ModelSubscriber::STATUS_SUBSCRIBED, ModelSubscriber::STATUS_UNSUBSCRIBED]])
            ->load();

        foreach ($dataKey as $key => $values) {
            $dataKey[$key] = '';
        }
       
        foreach ($subscriberCollection as $key => $subscriber) {
            $data[] = $dataKey;
            $data[$key]['customer_email'] = $subscriber->getData('customer_email');
            $data[$key]['subscriber_status'] = $subscriber->getData('subscriber_status');
            $data[$key]['store_id'] = $subscriber->getData('store_id');
        }
        return $data;
    }

    /**
     * Get Customers if is subscribed newsletter and prepare data.
     * return data
     * @return array
     */
    public function prepareDataToExport()
    {
        $subscriberCollection = $this->_subcriberCollectionFactory->create()
            ->showCustomerInfo()
            ->load();

        foreach ($subscriberCollection as $key => $subscriber) {
            $data[] = $subscriber->getData();
        }
        return $data;
    }

    /**
     * get data and billing address customers subscribed to newsletter
     * @param Subscriber\Collection     $subcriberCollection
     * @return array
     */
    public function getDataCustomerSubscribed()
    {

        $periodDateLastOrder = $this->getPeriodSubscriber(
            self::SETTINGS_CRONEXPORT_NB_PERIOD_ORDER,
            self::SETTINGS_CRONEXPORT_TYPE_PERIOD_ORDER
        );

        $periodSubscriberStatus = $this->getPeriodSubscriber(
            self::SETTINGS_CRONEXPORT_NB_PERIOD_SUBSCRIBER,
            self::SETTINGS_CRONEXPORT_TYPE_PERIOD_SUBSCRIBER
        );

        $periodDateLastOrder = date("Ymd", strtotime($periodDateLastOrder));
        $periodSubscriberStatus = date("Ymd", strtotime($periodSubscriberStatus));

        $salesOrderTable = $this->_resourceConnection->getConnection()->getTableName(self::TABLE_NAME_SALES_ORDER);
        $sqlSelect = new \Zend_Db_Expr(sprintf(
            "(SELECT 
                MAX(sales_order.created_at) AS date_last_order,
                COUNT(sales_order.entity_id) AS nb_commandes,
                SUM(sales_order.grand_total) AS CA_total_client,
                AVG(sales_order.grand_total) AS panier_moyen,
                MIN(sales_order.created_at) AS date_premiere_commande,
                sales_order.customer_id
            FROM %s AS sales_order
            GROUP BY sales_order.customer_id)",
            $salesOrderTable
        ));

        $connection = $this->_resourceConnection->getConnection();
        $queryGetCustomersSubscriber = $connection->select()
            ->from(
                ['customer_entity' => $connection->getTableName(self::TABLE_NAME_CUSTOMER_ENTITY)],
                self::COLUMN_CUSTOMER_ENTITY
            )
            ->joinLeft(
                ['newsletter_subscriber' => $connection->getTableName(self::TABLE_NAME_NEWSLETTER_SUBSCRIBER)],
                'customer_entity.entity_id = newsletter_subscriber.customer_id',
                self::COLUMN_CUSTOMER_NEWSLETTER_SUBSCRIBER
            )
            ->joinLeft(
                ['customer_address_entity' => $connection->getTableName(self::TABLE_NAME_CUSTOMER_ADDRESS_ENTITY)],
                'customer_address_entity.entity_id = customer_entity.default_billing',
                self::COLUMN_CUSTOMER_ADDRESS_ENTITY
            )
            ->joinLeft(
                ['sales_order' => $sqlSelect],
                'sales_order.customer_id = customer_entity.entity_id',
                self::COLUMN_SALES_ORDER
            )
            ->joinLeft(
                ['last_order' => $connection->getTableName(self::TABLE_NAME_SALES_ORDER)],
                'last_order.created_at = sales_order.date_last_order',
                self::COLUMN_LAST_ORDER
            )
            ->where('customer_entity.verified =?', 1)
            ->where('(
                sales_order.date_last_order > '.$periodDateLastOrder.' 
                OR newsletter_subscriber.change_status_at > '.$periodSubscriberStatus.'
            )')
            ->where('(
                newsletter_subscriber.subscriber_email IS NULL
                OR newsletter_subscriber.subscriber_status = '.ModelSubscriber::STATUS_SUBSCRIBED.'
                OR newsletter_subscriber.subscriber_status = '.ModelSubscriber::STATUS_UNSUBSCRIBED.'
            )')
            ->group('sales_order.customer_id');

        $dataCustomersSubscriber = $connection->fetchAll($queryGetCustomersSubscriber);
        $dataVisitorSubscriber = $this->getVisitorSubscriber();
        $customersVisitorData = array_merge($dataCustomersSubscriber, $dataVisitorSubscriber);
        return $customersVisitorData;
    }

    /**
     * get customer subscribed data and their orders to prepare CSV for Actice Campaign
     * @return array $customersData
     */
    public function prepareDataToExportActiveCampaign()
    {
        $customersData = [];
        $customers = $this->getDataCustomerSubscribed();

        foreach ($customers as $customer) {
            $dataByItemOrder = [];
            $viewStoreCode = $this->_storeRepository->getById($customer['store_id'])->getCode();
            $customer['langue'] = strtoupper($viewStoreCode);
            unset($customer['store_id']);

            if (isset($customer['customer_created_at'])) {

                $customer['nb_articles_derniere_commande'] = round($customer['nb_articles_derniere_commande']);
                $customer['panier_moyen'] = round($customer['panier_moyen'], 2);
                $customer['CA_total_client'] = round($customer['CA_total_client'], 2);
                $customer['montant_total_derniere_commande'] = round($customer['montant_total_derniere_commande'], 2);
                $customer['customer_firstname'] = ucfirst($customer['customer_firstname']);
                $customer['customer_lastname'] = strtoupper($customer['customer_lastname']);
                $customer['customer_address_city'] = strtoupper($customer['customer_address_city']);
                (empty($customer['subscriber_status'])) ? $customer['subscriber_status'] = 0 : '';
                ($customer['subscriber_status'] = 0 || $customer['subscriber_status'] = 1) ? $customer['contact_eligible'] = 1 : $customer['contact_eligible'] = 0;
            
                $dataByItemOrder = $this->prepareDataItemsByOrder($customer['id_last_order']);

                unset($customer['customer_id']);
                unset($customer['customer_address_entity_id']);
                unset($customer['customer_default_billing']);
                unset($customer['id_last_order']);
            }

            $customersData[] = $customer + $dataByItemOrder;
        }
        return $customersData;
    }

    /**
     * get data items contained in an order
     * @param int $order_id
     * @return array
     */
    public function getDataItemsByOrderId($orderId)
    {
        $connection = $this->_resourceConnection->getConnection();
        $salesOrderItemTable = $connection->getTableName(self::TABLE_NAME_SALES_ORDER_ITEM);
        $catalogCategoryProductTable = $connection->getTableName(self::TABLE_NAME_CATALOG_CATEGORY_PRODUCT);
        $catalogCategoryVarcharTable = $connection->getTableName(self::TABLE_NAME_CATALOG_CATEGORY_ENTITY_VARCHAR);
        $attributeId = $this->_eavAttribute->getIdByCode(\Magento\Catalog\Model\Category::ENTITY, 'url_path');

        $selectItems = $connection->select()
            ->from(
                ["sales_order_item" => $salesOrderItemTable],
                [
                    "name" => "sales_order_item.name",
                    "product_id" => "sales_order_item.item_id"
                ]
            )
            ->joinLeft(
                ['catalog_category_product' => $catalogCategoryProductTable],
                'catalog_category_product.product_id = sales_order_item.product_id'
            )
            ->joinLeft(
                ['catalog_category_entity_varchar' => $catalogCategoryVarcharTable],
                'catalog_category_entity_varchar.entity_id = catalog_category_product.category_id',
                ["names_category" => "GROUP_CONCAT(catalog_category_entity_varchar.value SEPARATOR '|' )"]
            )
            ->where("sales_order_item.order_id=?", $orderId)
            ->where("catalog_category_product.category_id IS NOT NULL")
            ->where("catalog_category_entity_varchar.attribute_id = ?", $attributeId)
            ->where("catalog_category_entity_varchar.store_id = ?", Store::DEFAULT_STORE_ID)
            ->group("sales_order_item.product_id");

        return $connection->fetchAll($selectItems);
    }

    /**
     * get and prepare data items contained in an order to export customers Active Campaign
     * @param int       $order_id
     * @return array    $data
     */
    public function prepareDataItemsByOrder($orderId)
    {
        $data = [
            'noms_derniers_produits_commandes' => '',
            'categories_produits_commandes' => '',
            'marques_produits_commandes' => ''
        ];
        if (!empty($orderId)) {
            $dataItems = $this->getDataItemsByOrderId($orderId);
            foreach ($dataItems as $dataItem) {
                $data['noms_derniers_produits_commandes'] .= (string) $dataItem['name'].self::SEPERATOR_IN_COLUMN;
                $namesCategory =  explode('|', $dataItem['names_category']);

                foreach ($namesCategory as $nameCategory) {
                    if ((stristr($nameCategory, 'marques/') || stristr($nameCategory, 'brands/'))
                        && !stristr($nameCategory, '/parfums/')) {
                        $nameCategory = explode('/', $nameCategory);
                        $brandName = str_replace('-', ' ', strtoupper($nameCategory[1]));
                        $data['marques_produits_commandes'] .= (string) $brandName.self::SEPERATOR_IN_COLUMN;
                    } else {
                        $nameCategory = explode('/', $nameCategory);
                        $nameCategory = end($nameCategory);
                        $nameCategory = str_replace('-', ' ', strtoupper($nameCategory));
                        $data['categories_produits_commandes'] .= (string) $nameCategory.self::SEPERATOR_IN_COLUMN;
                    }
                }
            }
        }
        $data = str_replace('"', '', $data);
        return $data;
    }

    public function export($download = false, $delimiter = '|')
    {
        $data = $this->prepareDataToExport();
        $path = self::PATH_SAVE_CSV_FILE;
        $fileName = self::FILE_NAME;
        return $this->exportCsvAdapter->export($data, $path, $fileName, $delimiter, $download);
    }

    public function exportToActiveCampaign($download = false)
    {
        $data = $this->prepareDataToExportActiveCampaign();
        $path = self::PATH_SAVE_CSV_ACTIVE_CAMPAIGN;
        $fileName = self::FILE_NAME_ACTIVE_CAMPAIGN;
        $delimiter = ';';
        return $this->exportCsvAdapter->export($data, $path, $fileName, $delimiter, $download);
    }
}
