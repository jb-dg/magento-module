<?php

namespace Origines\FooterSeo\Model;

use Origines\FooterSeo\Model\ResourceModel\FooterSeo\CollectionFactory;
use Origines\FooterSeo\Model\ResourceModel\FooterSeo as FooterSeoResourceModel;
use Origines\FooterSeo\Model\FooterSeo as FooterSeoModel;
use Exception;

class FooterSeoRepository
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var FooterSeoFactory
     */
    private $footerSeoFactory;

    /**
     * @var FooterSeoResourceModel
     */
    private $footerSeoResourceModel;

    /**
     * FooterSeoRepository constructor.
     * @param FooterSeoResourceModel $footerSeoResourceModel
     * @param FooterSeoFactory $footerSeoFactory
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        FooterSeoResourceModel $footerSeoResourceModel,
        FooterSeoFactory $footerSeoFactory,
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->footerSeoFactory = $footerSeoFactory;
        $this->footerSeoResourceModel = $footerSeoResourceModel;
    }

    /**
     * Get by id
     *
     * @param $id
     * @return FooterSeoModel
     */
    public function getById($id)
    {
        $footerSeo = $this->footerSeoFactory->create();
        $this->footerSeoResourceModel->load($footerSeo, $id);
        return $footerSeo;
    }

    /**
     * Get collection
     *
     * @return FooterSeoResourceModel\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * Save
     *
     * @param FooterSeoResourceModel $footerSeo
     * @return FooterSeoResourceModel|null
     */
    public function save(FooterSeoResourceModel $footerSeo)
    {
        try {
            $this->footerSeoResourceModel->save($footerSeo);
        } catch (Exception $e) {
            return null;
        }

        return $footerSeo;
    }

    /**
     * Delete
     *
     * @param FooterSeoResourceModel $footerSeo
     */
    public function delete(FooterSeoResourceModel $footerSeo)
    {
        $footerSeo->getResource()->delete($footerSeo);
    }

    /**
     * Delete by id
     *
     * @param $id
     * @throws \Exception
     */
    public function deleteById($id)
    {
        $footerSeo = $this->getById($id);
        $footerSeo->getResource()->delete($footerSeo);
    }
}
