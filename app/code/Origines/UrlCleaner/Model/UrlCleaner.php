<?php
namespace Origines\UrlCleaner\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Serialize\Serializer\Json;

class UrlCleaner extends \Magento\Framework\Model\AbstractModel
{
    private $serializer;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        Json $serializer = null
    ) {
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(\Origines\UrlCleaner\Model\ResourceModel\UrlCleaner::class);
        $this->_collectionName = \Origines\UrlCleaner\Model\ResourceModel\UrlCleanerCollection::class;
    }
}
